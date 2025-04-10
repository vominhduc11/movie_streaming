// public/assets/js/room.js

// Khởi tạo phòng xem phim
function initRoom() {
    // Nút rời phòng
    const leaveRoomButton = document.getElementById('leave-room');

    if (leaveRoomButton) {
        leaveRoomButton.addEventListener('click', function () {
            leaveRoom();
        });
    }

    // Kiểm tra khi trang đóng
    window.addEventListener('beforeunload', function (e) {
        // Rời phòng khi trang đóng
        leaveRoom();

        // Thông báo xác nhận
        e.preventDefault();
        e.returnValue = '';

        return '';
    });
}

// Thêm user mới vào danh sách
function addUserToList(user) {
    const usersList = document.getElementById('users-list');
    const usersCount = document.getElementById('users-count');
    const template = document.getElementById('user-item-template');

    if (!usersList || !template || !usersCount) return;

    // Kiểm tra user đã tồn tại chưa
    const existingUser = usersList.querySelector(`.user-item[data-user-id="${user.id}"]`);
    if (existingUser) return;

    // Tạo clone từ template
    const userItem = document.importNode(template.content, true).querySelector('.user-item');

    // Đặt thuộc tính và nội dung
    userItem.setAttribute('data-user-id', user.id);
    userItem.querySelector('.user-name').textContent = user.username;

    // Đặt avatar
    const avatarImg = userItem.querySelector('.user-avatar img');
    avatarImg.src = `/public/assets/uploads/${user.avatar || 'default-avatar.jpg'}`;
    avatarImg.alt = user.username;

    // Thêm vào danh sách
    usersList.appendChild(userItem);

    // Cập nhật số lượng người xem
    usersCount.textContent = parseInt(usersCount.textContent) + 1;
}

// Xóa user khỏi danh sách
function removeUserFromList(userId) {
    const usersList = document.getElementById('users-list');
    const usersCount = document.getElementById('users-count');

    if (!usersList || !usersCount) return;

    // Tìm user item
    const userItem = usersList.querySelector(`.user-item[data-user-id="${userId}"]`);

    if (userItem) {
        // Xóa khỏi DOM
        userItem.remove();

        // Cập nhật số lượng người xem
        usersCount.textContent = Math.max(0, parseInt(usersCount.textContent) - 1);
    }
}

// Cập nhật danh sách user
function updateUsersList(users) {
    const usersList = document.getElementById('users-list');
    const usersCount = document.getElementById('users-count');

    if (!usersList || !usersCount || !Array.isArray(users)) return;

    // Xóa tất cả user hiện tại
    usersList.innerHTML = '';

    // Thêm lại danh sách user mới
    users.forEach(user => {
        const userItem = document.createElement('li');
        userItem.className = 'user-item';
        userItem.setAttribute('data-user-id', user.id);

        userItem.innerHTML = `
            <div class="user-avatar">
                <img src="/public/assets/uploads/${user.avatar || 'default-avatar.jpg'}" alt="${user.username}">
            </div>
            <div class="user-name">${user.username}</div>
        `;

        usersList.appendChild(userItem);
    });

    // Cập nhật số lượng người xem
    usersCount.textContent = users.length;
}