<?php
// app/socket/SocketServer.php
namespace App\Socket;

use Ratchet\MessageComponentInterface;
use Ratchet\ConnectionInterface;
use App\Models\Room;
use App\Models\Chat;
use App\Models\User;

class SocketServer implements MessageComponentInterface
{
    protected $clients;
    protected $rooms;
    protected $userRooms;
    protected $adminRooms;
    protected $roomModel;
    protected $chatModel;
    protected $userModel;

    public function __construct()
    {
        $this->clients = new \SplObjectStorage;
        $this->rooms = [];
        $this->userRooms = [];
        $this->adminRooms = [];
        $this->roomModel = new Room();
        $this->chatModel = new Chat();
        $this->userModel = new User();

        echo "Socket Server khởi động...\n";
    }

    public function onOpen(ConnectionInterface $conn)
    {
        // Lưu kết nối mới
        $this->clients->attach($conn);

        echo "Kết nối mới! ({$conn->resourceId})\n";
    }

    public function onMessage(ConnectionInterface $from, $msg)
    {
        $data = json_decode($msg, true);

        if (!isset($data['action'])) {
            return;
        }

        echo "Nhận tin nhắn: {$data['action']} từ ({$from->resourceId})\n";

        switch ($data['action']) {
            case 'join_room':
                $this->handleJoinRoom($from, $data);
                break;

            case 'leave_room':
                $this->handleLeaveRoom($from, $data);
                break;

            case 'chat_message':
                $this->handleChatMessage($from, $data);
                break;

            case 'play_video':
                $this->handlePlayVideo($from, $data);
                break;

            case 'pause_video':
                $this->handlePauseVideo($from, $data);
                break;

            case 'video_seek':
                $this->handleVideoSeek($from, $data);
                break;

            case 'update_video_time':
                $this->handleUpdateVideoTime($from, $data);
                break;
        }
    }

    public function onClose(ConnectionInterface $conn)
    {
        // Xử lý người dùng rời phòng khi đóng kết nối
        if (isset($this->userRooms[$conn->resourceId])) {
            $roomId = $this->userRooms[$conn->resourceId]['room_id'];
            $userId = $this->userRooms[$conn->resourceId]['user_id'];

            // Cập nhật trạng thái rời phòng trong database
            $this->roomModel->removeUser($roomId, $userId);

            // Thông báo cho những người còn lại trong phòng
            $this->broadcastUserLeft($roomId, $userId);

            // Xóa thông tin người dùng
            unset($this->userRooms[$conn->resourceId]);

            // Xóa khỏi danh sách phòng
            if (isset($this->rooms[$roomId])) {
                foreach ($this->rooms[$roomId] as $key => $clientId) {
                    if ($clientId === $conn->resourceId) {
                        unset($this->rooms[$roomId][$key]);
                        break;
                    }
                }
            }
        }

        // Xử lý admin rời phòng
        if (isset($this->adminRooms[$conn->resourceId])) {
            $roomId = $this->adminRooms[$conn->resourceId]['room_id'];

            // Thông báo cho người dùng trong phòng
            $this->broadcastAdminLeft($roomId);

            // Xóa thông tin admin
            unset($this->adminRooms[$conn->resourceId]);
        }

        // Xóa kết nối
        $this->clients->detach($conn);

        echo "Kết nối {$conn->resourceId} đã đóng\n";
    }

    public function onError(ConnectionInterface $conn, \Exception $e)
    {
        echo "Lỗi: {$e->getMessage()}\n";

        $conn->close();
    }

    protected function handleJoinRoom(ConnectionInterface $conn, $data)
    {
        if (!isset($data['room_id']) || !isset($data['token'])) {
            return;
        }

        $roomId = $data['room_id'];
        $token = $data['token'];

        // Giải mã token
        $tokenData = json_decode(base64_decode($token), true);

        // Kiểm tra token hợp lệ
        if (!$tokenData) {
            $conn->send(json_encode([
                'action' => 'error',
                'message' => 'Token không hợp lệ'
            ]));
            return;
        }

        // Lấy thông tin phòng
        $room = $this->roomModel->getById($roomId);

        if (!$room) {
            $conn->send(json_encode([
                'action' => 'error',
                'message' => 'Phòng không tồn tại'
            ]));
            return;
        }

        // Kiểm tra phòng có đang mở không
        if ($room['status'] !== 'open') {
            $conn->send(json_encode([
                'action' => 'room_closed',
                'message' => 'Phòng đã đóng'
            ]));
            return;
        }

        // Xử lý tham gia phòng dựa trên loại người dùng
        if (isset($tokenData['admin_id'])) {
            // Admin tham gia phòng
            $adminId = $tokenData['admin_id'];

            // Lưu thông tin admin
            $this->adminRooms[$conn->resourceId] = [
                'room_id' => $roomId,
                'admin_id' => $adminId
            ];

            // Lưu kết nối vào phòng
            if (!isset($this->rooms[$roomId])) {
                $this->rooms[$roomId] = [];
            }
            $this->rooms[$roomId][] = $conn->resourceId;

            // Thông báo admin tham gia phòng
            $this->broadcastAdminJoined($roomId);

            echo "Admin {$adminId} đã tham gia phòng {$roomId}\n";
        } else if (isset($tokenData['user_id'])) {
            // User tham gia phòng
            $userId = $tokenData['user_id'];

            // Kiểm tra quyền xem phim
            if (!$this->roomModel->canUserWatch($userId, $roomId)) {
                $conn->send(json_encode([
                    'action' => 'error',
                    'message' => 'Bạn chưa mua phim này'
                ]));
                return;
            }

            // Lưu thông tin user
            $this->userRooms[$conn->resourceId] = [
                'room_id' => $roomId,
                'user_id' => $userId
            ];

            // Lưu kết nối vào phòng
            if (!isset($this->rooms[$roomId])) {
                $this->rooms[$roomId] = [];
            }
            $this->rooms[$roomId][] = $conn->resourceId;

            // Thêm user vào phòng trong database
            $this->roomModel->addUser($roomId, $userId);

            // Lấy danh sách user trong phòng
            $users = $this->roomModel->getUsers($roomId);

            // Thông báo user tham gia phòng
            $this->broadcastUserJoined($roomId, $userId, $users);

            // Thông báo thành công cho user
            $conn->send(json_encode([
                'action' => 'joined_room',
                'room_id' => $roomId,
                'current_time' => $room['current_time'],
                'users' => $users
            ]));

            echo "User {$userId} đã tham gia phòng {$roomId}\n";
        } else {
            $conn->send(json_encode([
                'action' => 'error',
                'message' => 'Token không hợp lệ'
            ]));
        }
    }

    protected function handleLeaveRoom(ConnectionInterface $conn, $data)
    {
        if (!isset($data['room_id'])) {
            return;
        }

        $roomId = $data['room_id'];

        // Kiểm tra user có trong phòng không
        if (isset($this->userRooms[$conn->resourceId])) {
            $userId = $this->userRooms[$conn->resourceId]['user_id'];

            // Cập nhật trạng thái rời phòng trong database
            $this->roomModel->removeUser($roomId, $userId);

            // Thông báo cho những người còn lại trong phòng
            $this->broadcastUserLeft($roomId, $userId);

            // Xóa thông tin người dùng
            unset($this->userRooms[$conn->resourceId]);

            echo "User {$userId} đã rời phòng {$roomId}\n";
        }

        // Kiểm tra admin có trong phòng không
        if (isset($this->adminRooms[$conn->resourceId])) {
            // Thông báo cho người dùng trong phòng
            $this->broadcastAdminLeft($roomId);

            // Xóa thông tin admin
            unset($this->adminRooms[$conn->resourceId]);

            echo "Admin đã rời phòng {$roomId}\n";
        }

        // Xóa khỏi danh sách phòng
        if (isset($this->rooms[$roomId])) {
            foreach ($this->rooms[$roomId] as $key => $clientId) {
                if ($clientId === $conn->resourceId) {
                    unset($this->rooms[$roomId][$key]);
                    break;
                }
            }
        }
    }

    protected function handleChatMessage(ConnectionInterface $from, $data)
    {
        if (!isset($data['room_id']) || !isset($data['message'])) {
            return;
        }

        $roomId = $data['room_id'];
        $message = $data['message'];
        $time = date('H:i:s');

        // Kiểm tra người gửi là user hay admin
        $senderId = null;
        $isAdmin = false;

        if (isset($this->userRooms[$from->resourceId])) {
            $senderId = $this->userRooms[$from->resourceId]['user_id'];
        } else if (isset($this->adminRooms[$from->resourceId])) {
            $senderId = $this->adminRooms[$from->resourceId]['admin_id'];
            $isAdmin = true;
        } else {
            return;
        }

        // Lưu tin nhắn vào database
        $chatData = [
            'room_id' => $roomId,
            'user_id' => $isAdmin ? null : $senderId,
            'admin_id' => $isAdmin ? $senderId : null,
            'message' => $message,
            'created_at' => date('Y-m-d H:i:s')
        ];

        $chatId = $this->chatModel->addMessage($chatData);

        // Chuẩn bị dữ liệu gửi đi
        $broadcastData = [
            'action' => 'chat_message',
            'room_id' => $roomId,
            'user_id' => $isAdmin ? null : $senderId,
            'admin_id' => $isAdmin ? $senderId : null,
            'message' => $message,
            'time' => $time
        ];

        // Gửi tin nhắn đến tất cả người dùng trong phòng
        $this->broadcastToRoom($roomId, $broadcastData);

        echo "Tin nhắn mới trong phòng {$roomId}\n";
    }

    protected function handlePlayVideo(ConnectionInterface $from, $data)
    {
        if (!isset($data['room_id'])) {
            return;
        }

        $roomId = $data['room_id'];

        // Chỉ admin mới có thể điều khiển video
        if (!isset($this->adminRooms[$from->resourceId])) {
            return;
        }

        // Gửi lệnh play đến tất cả người dùng trong phòng
        $this->broadcastToRoom($roomId, [
            'action' => 'video_play',
            'room_id' => $roomId
        ]);

        echo "Admin phát video trong phòng {$roomId}\n";
    }

    protected function handlePauseVideo(ConnectionInterface $from, $data)
    {
        if (!isset($data['room_id'])) {
            return;
        }

        $roomId = $data['room_id'];

        // Chỉ admin mới có thể điều khiển video
        if (!isset($this->adminRooms[$from->resourceId])) {
            return;
        }

        // Gửi lệnh pause đến tất cả người dùng trong phòng
        $this->broadcastToRoom($roomId, [
            'action' => 'video_pause',
            'room_id' => $roomId
        ]);

        echo "Admin tạm dừng video trong phòng {$roomId}\n";
    }

    protected function handleVideoSeek(ConnectionInterface $from, $data)
    {
        if (!isset($data['room_id']) || !isset($data['time'])) {
            return;
        }

        $roomId = $data['room_id'];
        $time = $data['time'];

        // Chỉ admin mới có thể điều khiển video
        if (!isset($this->adminRooms[$from->resourceId])) {
            return;
        }

        // Cập nhật thời gian hiện tại trong database
        $this->roomModel->updateCurrentTime($roomId, $time);

        // Gửi lệnh seek đến tất cả người dùng trong phòng
        $this->broadcastToRoom($roomId, [
            'action' => 'video_seek',
            'room_id' => $roomId,
            'time' => $time
        ]);

        echo "Admin tua video đến {$time}s trong phòng {$roomId}\n";
    }

    protected function handleUpdateVideoTime(ConnectionInterface $from, $data)
    {
        if (!isset($data['room_id']) || !isset($data['time'])) {
            return;
        }

        $roomId = $data['room_id'];
        $time = $data['time'];

        // Cập nhật thời gian hiện tại trong database (chỉ khi gửi từ admin)
        if (isset($this->adminRooms[$from->resourceId])) {
            $this->roomModel->updateCurrentTime($roomId, $time);
        }
    }

    protected function broadcastToRoom($roomId, $data)
    {
        if (!isset($this->rooms[$roomId])) {
            return;
        }

        $dataString = json_encode($data);

        foreach ($this->rooms[$roomId] as $clientId) {
            foreach ($this->clients as $client) {
                if ($client->resourceId === $clientId) {
                    $client->send($dataString);
                    break;
                }
            }
        }
    }

    protected function broadcastUserJoined($roomId, $userId, $users)
    {
        $this->broadcastToRoom($roomId, [
            'action' => 'user_joined',
            'room_id' => $roomId,
            'user_id' => $userId,
            'users' => $users
        ]);
    }

    protected function broadcastUserLeft($roomId, $userId)
    {
        $this->broadcastToRoom($roomId, [
            'action' => 'user_left',
            'room_id' => $roomId,
            'user_id' => $userId
        ]);
    }

    protected function broadcastAdminJoined($roomId)
    {
        $this->broadcastToRoom($roomId, [
            'action' => 'admin_joined',
            'room_id' => $roomId
        ]);
    }

    protected function broadcastAdminLeft($roomId)
    {
        $this->broadcastToRoom($roomId, [
            'action' => 'admin_left',
            'room_id' => $roomId
        ]);
    }
}
