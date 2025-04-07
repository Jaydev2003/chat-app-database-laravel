<x-app-layout>
    <div class="flex">
        <div class="message-container"></div>
        <div class="notification-container" id="notification-container"></div>
        <aside
            class="w-1/4 bg-gray-200 dark:bg-gray-900 p-4 overflow-y-auto shadow-lg border-r border-gray-300 dark:border-gray-700">
            <div class="flex justify-between items-center mb-4">
                <h2 id="people-text" class="text-xl font-semibold text-gray-800 dark:text-gray-200">People</h2>
                <button id="makeGroupBtn"
                    class="make-group-btn rounded-lg bg-gray-100 dark:bg-gray-800 hover:bg-gray-300 dark:hover:bg-gray-700 text-white text-sm font-semibold">
                    <i class="fas fa-users mr-2 "></i> New Group
                </button>
            </div>
            <input type="text" id="searchUser"
                class="w-full mb-2 p-2 rounded-lg border border-gray-300 dark:border-gray-700 bg-gray-100 dark:bg-gray-800 text-gray-900 dark:text-gray-200"
                placeholder="Search users...">

            <ul id="userList" class="max-h-full">
                @foreach ($chatList as $item)
                                @php
                                    $unreadCount = $unreadCounts[$item->id] ?? 0;
                                    $isGroup = $item->type === 'group';
                                    $imagePath = $isGroup
                                        ? ($item->group_img
                                            ? asset('storage/' . $item->group_img)
                                            : asset('default-group.png'))
                                        : ($item->profile_picture
                                            ? asset('storage/' . $item->profile_picture)
                                            : asset('default-user.png'));
                                @endphp

                                <li class="mb-2 flex items-center">
                                    <input type="checkbox"
                                        class="user-checkbox hidden me-2 rounded-full w-5 h-5 border-gray-300 dark:border-gray-700"
                                        data-user-id="{{ $item->id }}" data-user-name="{{ $item->name }}">

                                    <button class="profile-btn relative me-1" data-receiver-id="{{ $item->id }}"
                                        data-user-id="{{ $item->id }}" data-profile="{{ $imagePath }}">
                                        <div class="w-10 h-10 rounded-full overflow-hidden border-2 border-black">
                                            <img src="{{ $imagePath }}" alt="Profile Picture" class="w-full h-full object-cover">
                                        </div>
                                        @if ($item instanceof \App\Models\User && $item->isOnline())
                                            <span
                                                class="absolute bottom-0 right-0 w-3 h-3 bg-green-500 rounded-full border-2 border-black shadow-md"></span>
                                        @else
                                            <span
                                                class="absolute bottom-0 right-0 w-3 h-3 bg-red-500 rounded-full border-2 border-black shadow-md"></span>
                                        @endif
                                    </button>

                                    <button
                                        class="user-btn flex items-center space-x-2 p-2 w-full text-left rounded-lg bg-gray-100 dark:bg-gray-800 hover:bg-gray-300 dark:hover:bg-gray-700"
                                        data-receiver-id="{{ $item->id }}" data-user-name="{{ $item->name }}"
                                        data-type="{{ $item->type }}" data-profile="{{ $imagePath }}">
                                        <span class="text-lg font-medium text-gray-900 dark:text-gray-200">
                                            {{ $item->name }}
                                        </span>
                                        @if ($unreadCount > 0)
                                            <span
                                                class="ml-auto bg-red-500 text-white text-xs font-semibold px-2 py-1 rounded-full unread-count"
                                                data-user-id="{{ $item->id }}">
                                                {{ $unreadCount }}
                                            </span>
                                        @endif
                                    </button>
                                    <span id="user-status-{{ $item->id }}" class="hidden">
                                        @if ($item instanceof \App\Models\User)
                                            @if ($item->isOnline())
                                                <span class="online hidden">Online</span>
                                            @else
                                                <span class="last-seen hidden">{{ $item->lastSeen() }}</span>
                                            @endif
                                        @else
                                            <span class="group-chat hidden">Group Chat</span>
                                        @endif
                                    </span>
                                </li>
                @endforeach
            </ul>
            <ul id="groupList"></ul>
            <div class="button-container flex justify-between">
                <button id="confirmGroupBtn"
                    class="hidden w-1/2 mt-2 p-2 bg-blue-500 text-white rounded-lg flex items-center justify-center">
                    <i class="fas fa-check-circle mr-2"></i>
                </button>

                <button id="cancelGroupBtn"
                    class="hidden w-1/2 mt-2 p-2 bg-gray-500 text-white rounded-lg flex items-center justify-center">
                    <i class="fas fa-times-circle mr-2"></i>
                </button>
            </div>

        </aside>

        <div id="defaultScreen" class="w-3/4 p-6 flex flex-col items-center justify-center text-center">
            <p class="text-gray-500 dark:text-gray-400 text-2xl font-semibold">
                Welcome to the Chat App!
            </p>
            <p class="text-gray-500 dark:text-gray-400 text-sm mt-2">
                Secure by End-to-End Encryption
            </p>
        </div>

        <div id="chatContainer" class="w-3/4 p-4 flex flex-col hidden mt-4">
            <div class="max-w-3xl mx-auto w-full h-full">
                <div class="bg-white dark:bg-gray-800 shadow-lg rounded-2xl p-4 h-full flex flex-col">
                    <div id="chat-header"
                        class="flex items-center gap-3 border-b border-gray-300 dark:border-gray-700 pb-2">
                        <img id="profileImage" alt="Profile Picture"
                            class="w-10 h-10 rounded-full object-cover border border-gray-300 ">
                        <div>
                            <button id="chatTitle"
                                class="btn btn-primary text-lg font-semibold text-gray-900 dark:text-gray-200">
                            </button>
                            <p id="last_seen" class="text-gray-900 dark:text-gray-200"></p>
                        </div>

                    </div>

                    <div id="chat-box" class="flex-1 overflow-auto p-4 rounded-xl bg-gray-100 dark:bg-gray-900"></div>

                    <div id="filePreviewContainer" class="mt-2 hidden flex flex-col space-y-2"></div>

                    <div class="flex items-center p-2 rounded-xl bg-gray-200 dark:bg-gray-800 relative">


                        <button id="uploadBtn" class="ml-2 text-blue-500 dark:text-blue-400 relative">
                            <i class="fas fa-plus-circle text-xl"></i>
                        </button>

                        <button id="emojiPickerBtn" class="ml-2 mr-2 text-yellow-500 dark:text-blue-400">
                            <i class="fas fa-smile text-xl"></i>
                        </button>
                        <div id="emojiPickerContainer"
                            class="absolute bottom-12 left-10 hidden z-50 bg-white dark:bg-gray-700 shadow-lg rounded-lg">
                        </div>

                        <div id="uploadOptions" class="upload-options hidden">
                            <button id="openCamera"><i class="fas fa-camera"></i>Camera</button>
                            <button id="openFile"><i class="fas fa-folder-open"></i> File Explorer</button>
                        </div>

                        <input type="file" id="fileInput" class="hidden" accept="image/*,video/*,.pdf,.doc,.docx">

                        <input type="text" id="message"
                            class="w-full px-3 py-2 bg-transparent outline-none text-gray-900 dark:text-gray-200 rounded-full border border-gray-300 dark:border-gray-600"
                            placeholder="Type a message..." disabled>

                        <button id="sendBtn" class="ml-2 text-blue-500 dark:text-blue-400">
                            <i class="fas fa-paper-plane text-xl"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>
        <div id="cameraModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex justify-center items-center">
            <div class="bg-white p-4 rounded-lg shadow-lg flex flex-col items-center">
                <video id="cameraFeed" width="500" height="400" autoplay class="border border-gray-300 rounded"></video>
                <div class="mt-4 flex space-x-4">
                    <button id="captureImage" class="bg-blue-500 text-white px-4 py-2 rounded">Capture Image</button>
                    <button id="closeCamera" class="bg-red-500 text-white px-4 py-2 rounded">Close Camera</button>
                </div>
            </div>
        </div>

        <div id="profileModal" class="fixed inset-0 flex items-center justify-center bg-gray-900 bg-opacity-50 hidden">
            <div class="bg-white dark:bg-gray-800 p-5 rounded-lg shadow-lg w-96">
                <h2 class="text-lg font-semibold mb-4 text-gray-900 dark:text-gray-200">Set Profile Picture</h2>

                <input type="file" id="profileInput" accept="image/*"
                    class="block w-full mb-4 text-gray-700 dark:text-gray-300  cursor-pointer">

                <img id="previewImage" class="w-24 h-24 rounded-full object-cover mx-auto border border-gray-300">

                <div class="mt-4 flex justify-end gap-3">
                    <button id="cancelBtn" class="px-4 py-2 bg-gray-500 text-white rounded-lg">Cancel</button>
                    <button id="saveBtn" class="px-4 py-2 bg-blue-600 text-white rounded-lg">Save</button>
                </div>
            </div>
        </div>

        <div id="groupModal" class="fixed inset-0 hidden bg-gray-900 bg-opacity-50 flex items-center justify-center">
            <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-lg w-1/3">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-200 mb-4">Create Group</h3>

                <label class="block mb-2 text-gray-700 dark:text-gray-300">Group Name</label>
                <input type="text" id="groupName" class="w-full p-2 border rounded-lg mb-4"
                    placeholder="Enter group name">

                <label class="block mb-2 text-gray-700 dark:text-gray-300">Upload Group Image</label>
                <input type="file" id="groupImage" accept="image/*" class="w-full p-2 border rounded-lg mb-4">

                <div id="imagePreviewContainer" class="hidden">
                    <img id="imagePreview" class="w-24 h-24 rounded-lg mx-auto mb-4" src="" alt="Preview">
                </div>

                <h4 class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Selected Users:</h4>
                <ul id="selectedUsersList" class="mb-4 text-lg font-medium text-gray-900"></ul>

                <div class="flex justify-end">
                    <button id="closeModal" class="mr-2 bg-gray-400 text-white px-4 py-2 rounded">Cancel</button>
                    <button id="createGroup" class="bg-blue-500 text-white px-4 py-2 rounded">Create</button>
                </div>
            </div>
        </div>
        <div id="groupMembersModal"
            class="fixed inset-0 flex items-center justify-center bg-gray-900 bg-opacity-50 hidden">
            <div class="bg-white dark:bg-gray-800 p-5 rounded-lg shadow-lg w-96 relative">

                <button id="closeGroupModal"
                    class="absolute top-2 right-2 text-gray-600 dark:text-gray-300 text-xl">&times;</button>

                <h2 class="text-lg font-semibold mb-4 text-gray-900 dark:text-gray-200">Group Members</h2>

                <ul id="groupMembersList" class="space-y-2 text-gray-800 dark:text-gray-200">
                    <li>Loading...</li>
                </ul>

                <div class="mt-4 flex justify-end gap-3">
                    <button id="DeleteGroup" class="px-4 py-2 bg-gray-500 text-white rounded-lg">Delete</button>
                </div>
                <div class="mt-4 flex justify-end gap-3">
                    <button id="cancelGroupBtn" class="px-4 py-2 bg-gray-500 text-white rounded-lg">Close</button>
                </div>
            </div>
        </div>
    </div>
    <script>
        $(document).ready(function () {
            const $emojiPickerBtn = $("#emojiPickerBtn");
            const $emojiPickerContainer = $("#emojiPickerContainer");
            const $messageInput = $("#message");

            let picker = new EmojiMart.Picker({
                set: "apple",
                onEmojiSelect: function (emoji) {
                    $messageInput.val($messageInput.val() + emoji.native);
                },
            });

            $emojiPickerContainer.append(picker);

            $emojiPickerBtn.on("click", function (event) {
                event.stopPropagation();
                $emojiPickerContainer.toggleClass("hidden");
            });


            $(document).on("click", function (event) {
                if (!$emojiPickerContainer.is(event.target) && !$emojiPickerBtn.is(event.target) &&
                    $emojiPickerContainer.has(event.target).length === 0) {
                    $emojiPickerContainer.addClass("hidden");
                }
            });


            setInterval(() => {
                fetch('/keep-alive', {
                    method: 'GET',
                    credentials: 'include'
                });
            }, 30000);

            document.getElementById("makeGroupBtn").addEventListener("click", function () {
                document.querySelectorAll(".user-checkbox").forEach(checkbox => checkbox.classList.toggle(
                    "hidden"));
                document.getElementById("confirmGroupBtn").classList.toggle("hidden");
                document.getElementById('confirmGroupBtn').classList.remove('hidden');
                document.getElementById('cancelGroupBtn').classList.remove('hidden');
            });

            document.getElementById('cancelGroupBtn').addEventListener('click', function () {

                document.getElementById('confirmGroupBtn').classList.add('hidden');
                document.getElementById('cancelGroupBtn').classList.add('hidden');


                const checkboxes = document.querySelectorAll('.user-checkbox');

                checkboxes.forEach(checkbox => {
                    checkbox.checked = false;
                    checkbox.classList.add('hidden');
                });
            });


            document.getElementById("confirmGroupBtn").addEventListener("click", function () {
                let selectedUsers = [];
                let selectedUserNames = [];

                document.querySelectorAll(".user-checkbox:checked").forEach(checkbox => {
                    selectedUsers.push(checkbox.getAttribute("data-user-id"));
                    selectedUserNames.push(checkbox.getAttribute("data-user-name"));
                });

                if (selectedUsers.length > 1) {
                    let selectedUsersList = document.getElementById("selectedUsersList");
                    selectedUsersList.innerHTML = "";

                    selectedUsers.forEach((userId, index) => {
                        let userBox = document.createElement("div");
                        userBox.classList.add("flex", "items-center", "mb-2", "space-x-2", "p-2",
                            "w-full",
                            "rounded-lg", "bg-gray-300", "dark:bg-gray-700");

                        userBox.innerHTML = `
                <div class="flex items-center justify-between w-full">
                    <div class="flex items-center space-x-2">
                        <i class="fas fa-user-circle fa-2x text-gray-500"></i>
                        <span class="text-lg font-medium text-gray-900 dark:text-gray-200">${selectedUserNames[index]}</span>
                    </div>
                    <button class="cancelBtn">
                        <i class="fas fa-times-circle text-gray-500 dark:text-gray-200"></i>
                    </button>
                </div>
            `;

                        selectedUsersList.appendChild(userBox);

                        userBox.querySelector(".cancelBtn").addEventListener("click", function () {
                            userBox.remove();
                            document.querySelector(
                                `.user-checkbox[data-user-id="${userId}"]`).checked =
                                false;

                            if (selectedUsersList.children.length === 0) {
                                document.getElementById("groupModal").classList.add(
                                    "hidden");
                            }
                        });
                    });

                    document.getElementById("groupModal").classList.remove("hidden");
                    document.getElementById('confirmGroupBtn').classList.add('hidden');
                    document.getElementById('cancelGroupBtn').classList.add('hidden');
                } else {
                    let notification = document.createElement("div");
                    notification.className =
                        "p-2 bg-red-500 text-white text-center rounded-lg mb-2";
                    notification.innerText = "Select at least Two users";
                    document.getElementById("notification-container").appendChild(notification);
                    setTimeout(() => {
                        notification.remove();
                    }, 3000);

                }

            });

            document.getElementById("closeModal").addEventListener("click", function () {
                document.getElementById("groupModal").classList.add("hidden");
                const checkboxes = document.querySelectorAll('.user-checkbox');
                checkboxes.forEach(checkbox => checkbox.checked = false);
                document.querySelectorAll(".user-checkbox").forEach(checkbox => checkbox.classList.toggle(
                    "hidden"));
            });

            document.getElementById("createGroup").addEventListener("click", function () {
                let groupName = document.getElementById("groupName").value.trim();
                let selectedUsers = [];
                let groupImage = document.getElementById("groupImage").files[0];

                document.querySelectorAll(".user-checkbox:checked").forEach(checkbox => {
                    selectedUsers.push(checkbox.getAttribute("data-user-id"));
                });

                if (!groupName || selectedUsers.length === 0) {
                    console.error("Group name and at least one user must be selected.");
                    return;
                }

                let formData = new FormData();
                formData.append("name", groupName);
                selectedUsers.forEach(userId => formData.append("users[]", userId));
                if (groupImage) {
                    formData.append("group_image", groupImage);
                }

                fetch("/groups", {
                    method: "POST",
                    headers: {
                        "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]')
                            .getAttribute("content")
                    },
                    body: formData
                })
                    .then(response => {
                        if (!response.ok) throw new Error("Server Error");
                        return response.json();
                    })
                    .then(data => {
                        if (!data.success) throw new Error(data.message);

                        let notification = document.createElement("div");
                        notification.className =
                            "p-2 bg-green-500 text-white text-center rounded-lg mb-2";
                        notification.innerText = "Group created successfully!";
                        document.getElementById("notification-container").appendChild(notification);

                        setTimeout(() => {
                            notification.remove();
                        }, 3000);

                        let groupList = document.getElementById("userList");
                        let groupItem = document.createElement("li");
                        groupItem.classList.add("mb-2", "flex", "items-center");
                        const groupImage = data.group.image ? data.group.image : '/default-group.png';

                        groupItem.innerHTML = `
            <button class="user-btn flex items-center space-x-2 p-2 w-full text-left rounded-lg bg-gray-100 dark:bg-gray-800 hover:bg-gray-300 dark:hover:bg-gray-700"
                data-receiver-id="${data.group.id}" data-user-name="${data.group.name}">
                <img src="${groupImage}" class="w-10 h-10 rounded-full" alt="Group Image">
                <span class="text-lg font-medium text-gray-900 dark:text-gray-200">${data.group.name}</span>
            </button>
        `;

                        groupList.appendChild(groupItem);

                        groupItem.querySelector(".user-btn").addEventListener("click", function () {
                            let receiverId = this.getAttribute("data-receiver-id");
                            let userName = this.getAttribute("data-user-name");
                            fetchMessages(receiverId, userName, profileImage);
                        });

                        document.getElementById("groupModal").classList.add("hidden");
                        document.getElementById("groupName").value = "";
                        document.getElementById("groupImage").value = "";
                        document.getElementById("imagePreview").src = "";
                        document.getElementById("imagePreviewContainer").classList.add("hidden");

                        document.querySelectorAll(".user-checkbox:checked").forEach(checkbox => checkbox
                            .checked = false);
                        document.getElementById("confirmGroupBtn").classList.add("hidden");
                        document.getElementById("cancelGroupBtn").classList.add("hidden");

                        location.reload();
                    })
                    .catch(error => console.error("Error:", error.message));
            });

            document.getElementById("groupImage").addEventListener("change", function (event) {
                const file = event.target.files[0];
                if (file) {
                    const reader = new FileReader();
                    reader.onload = function (e) {
                        document.getElementById("imagePreview").src = e.target.result;
                        document.getElementById("imagePreviewContainer").classList.remove("hidden");
                    };
                    reader.readAsDataURL(file);
                }
            });


            document.getElementById("searchUser").addEventListener("input", function () {
                let searchQuery = this.value.toLowerCase();
                let noResultsMessage = document.getElementById("noResultsMessage");

                let items = document.querySelectorAll("#userList li");
                let found = false;

                items.forEach(li => {
                    let userName = li.querySelector("span").textContent.toLowerCase();
                    if (userName.includes(searchQuery)) {
                        li.style.display = "flex";
                        found = true;
                    } else {
                        li.style.display = "none";
                    }
                });


                if (!found && searchQuery) {
                    if (!noResultsMessage) {
                        let message = document.createElement("li");
                        message.id = "noResultsMessage";
                        message.textContent = "No user found";
                        message.classList.add("text-center", "text-gray-500", "mt-3");
                        document.getElementById("userList").appendChild(message);
                    }
                } else if (found && noResultsMessage) {
                    noResultsMessage.remove();
                }
            });



            let userId = "{{ Auth::id() }}";
            let receiverId = null;

            function fetchMessages(receiverId, userName, profileImage) {
                $("#defaultScreen").addClass("hidden");
                $("#chatContainer").removeClass("hidden");
                $("#chatTitle").text(`${userName}`);
                $("#chatTitle").attr("data-group-id", receiverId);

                let profileImageSrc = profileImage ? `${profileImage}` : "default-user.png";
                console.log(profileImageSrc);
                $("#profileImage").attr("src", profileImageSrc);
                let previewImageSrc = profileImage ? `${profileImage}` : "default-user.png";
                $("#previewImage").attr("src", previewImageSrc);
                $("#message").prop("disabled", false);
                $("#sendBtn").prop("disabled", false);

                $.ajax({
                    url: "{{ route('fetch.messages') }}",
                    method: "POST",
                    data: {
                        receiver_id: receiverId,
                        _token: "{{ csrf_token() }}"
                    },
                    success: function (response) {
                        let messagesHtml = response.messages.map(msg => {
                            let isSentByUser = msg.sender_id == userId;
                            let messageContent = "";

                            if (msg.file_url) {
                                let messageText = msg.message || "";
                                let extractedMessage = "";

                                if (messageText.includes(" || ")) {
                                    [extractedMessage] = messageText.split(" || ");
                                }

                                messageContent += `
                        <div class="mt-2">
                            ${displayMediaType(msg.file_url, msg.fileType, msg.original_file_name, msg.sender)}
                        </div>
                    `;

                                if (extractedMessage.trim() !== "") {
                                    messageContent +=
                                        `<p class="text-sm mt-1">${extractedMessage}</p>`;
                                }
                            }

                            if (!msg.file_url && msg.message && msg.message.trim() !== "") {
                                messageContent += `<p class="text-sm">${msg.message}</p>`;
                            }

                            return `
                    <div class="flex flex-col ${isSentByUser ? 'items-end' : 'items-start'} mb-2">
                        <div class="rounded-lg px-4 py-2 max-w-xs shadow-md
                            ${isSentByUser ? 'dark:bg-gray-700 text-white' : 'bg-gray-300 dark:bg-gray-700 text-gray-900 dark:text-gray-200'}">
                            <span class="font-semibold block">${msg.sender ? msg.sender.name : 'Unknown'}</span>
                            ${messageContent}
                        </div>
                        <span class="text-xs text-gray-500 dark:text-gray-400 mt-1">${msg.time}</span>
                    </div>
                `;
                        }).join('');

                        if (response.messages.length > 0) {
                            $("#chat-box").html(messagesHtml);
                        } else {
                            $("#chat-box").html(
                                `<p id="nomessagetext" class="text-center text-gray-500 dark:text-gray-400">No messages yet.</p>`
                            );
                        }
                        $("#chat-box").scrollTop($("#chat-box")[0].scrollHeight);
                    }
                });
            }

            let receiverId = null;
            let chatType = null;
            $(document).on("click", ".user-btn", function () {
                receiverId = $(this).data("receiver-id");
                let userName = $(this).data("user-name");
                let profileImage = $(this).data("profile");
                chatType = $(this).data("type");

                let statusElement = $("#user-status-" + receiverId);
                let lastSeenText = statusElement.find(".last-seen").text().trim();
                let onlineStatus = statusElement.find(".online").text().trim();
                let groupChatText = statusElement.find(".group-chat").text().trim();

                $("#previewImage").attr("data-user-id", receiverId);
                $("#chatTitle").text(userName).data("chat-type", chatType);

                if (groupChatText !== "") {
                    $("#last_seen").text(groupChatText).removeClass("hidden");
                } else if (onlineStatus === "Online") {
                    $("#last_seen").text("Online").removeClass("hidden");
                } else if (lastSeenText && lastSeenText !== "Offline") {
                    $("#last_seen").text("Last seen " + lastSeenText).removeClass("hidden");
                } else {
                    $("#last_seen").addClass("hidden");
                }
                fetchMessages(receiverId, userName, profileImage);
            });

            $("#chatTitle").click(function () {
                let chatType = $(this).data("chat-type");
                if (chatType !== "group") {
                    return;
                }
                let groupId = receiverId;
                $("#DeleteGroup").attr("data-group-id", groupId);

                $.ajax({
                    url: "{{ route('group-member') }}",
                    method: "POST",
                    data: {
                        group_id: groupId,
                        _token: "{{ csrf_token() }}"
                    },
                    success: function (response) {
                        let membersHtml = "";

                        if (response.members.length > 0) {
                            response.members.forEach(member => {
                                membersHtml +=
                                    `<li class="flex items-center space-x-2 p-2 w-full text-left rounded-lg bg-gray-100 dark:bg-gray-800 hover:bg-gray-300 dark:hover:bg-gray-700">${member.user.name} (${member.user.email})</li>`;
                            });
                        } else {
                            membersHtml = "<li>No members found.</li>";
                        }

                        $("#groupMembersList").html(membersHtml);
                        $("#groupMembersModal").removeClass("hidden");
                    },
                    error: function () {
                        $("#groupMembersList").html("<li>Error loading members.</li>");
                    }
                });

                $(document).on("click", "#DeleteGroup", function () {
                    let groupId = $(this).data("group-id");
                    console.log(groupId);

                    if (!groupId) {
                        message("Invalid group ID.", 'error');
                        return;
                    }

                    if (!confirm("Are you sure you want to delete this group?")) {
                        return;
                    }

                    $.ajax({
                        url: "/delete-group/" + groupId,
                        type: "DELETE",
                        data: {
                            _token: $('meta[name="csrf-token"]').attr("content")
                        },
                        success: function (response) {
                            if (response.status === "success") {
                                message("Group deleted successfully.", 'success');
                                $("#groupMembersModal").addClass("hidden");
                                $("#chatContainer").addClass("hidden");
                                $("#defaultScreen").removeClass("hidden");


                                $("#group-" + groupId).fadeOut("slow", function () {
                                    $(this).remove();
                                });

                                setTimeout(function () {
                                    location.reload();
                                });
                            } else {
                                message("Failed to delete the group.", 'error');
                            }
                        },
                        error: function (xhr) {
                            console.error(xhr.responseText);
                            message("An error occurred. Please try again.", 'error');
                        }
                    });
                });

                function closeModal() {
                    $("#groupMembersModal").addClass("hidden");
                }

                $("#closeGroupModal, #cancelGroupBtn").click(closeModal);


                $("#groupMembersModal").click(function (event) {
                    if ($(event.target).is("#groupMembersModal")) {
                        closeModal();
                    }
                });
            });




            $(document).on("click", ".profile-btn", function (event) {
                event.stopPropagation();
                receiverId = $(this).data("receiver-id");
                let profileImage = $(this).data("profile");

                $("#previewImage").attr("data-user-id", receiverId);
                $("#previewImage").attr("src", profileImage);
                $("#profileModal").removeClass("hidden");
            });

            $("#cancelBtn").click(function () {
                $("#profileModal").addClass("hidden");
            });

            $("#profileInput").on("change", function () {
                const file = this.files[0];
                if (file) {
                    const reader = new FileReader();
                    reader.onload = function (e) {
                        $("#previewImage").attr("src", e.target.result);
                    };
                    reader.readAsDataURL(file);
                }
            });

            $("#saveBtn").click(function () {
                let formData = new FormData();
                let file = $("#profileInput")[0].files[0];

                if (!file) {
                    message("Please select an image.", 'error');
                    return;
                }

                let userId = $("#previewImage").attr("data-user-id");
                console.log(userId);

                formData.append("profile_picture", file);
                formData.append("_token", $('meta[name="csrf-token"]').attr("content"));

                $.ajax({
                    url: "/update-profile-img/" + userId,
                    method: "POST",
                    data: formData,
                    contentType: false,
                    processData: false,
                    success: function (response) {
                        console.log(response);
                        $("#profileImage").attr("src", response.image);


                        $(".profile-btn[data-user-id='" + userId + "'] img").attr("src",
                            response.image);
                        $(".profile-btn[data-user-id='" + userId + "'] ").attr("data-profile",
                            response.image);
                        $("#profileModal").addClass("hidden");
                    },
                    error: function (xhr) {
                        console.error("Error:", xhr.responseText);
                    }
                });
            });



            let selectedFile = null;


            $("#uploadBtn").click(function (e) {
                e.stopPropagation();
                $("#uploadOptions").toggleClass("hidden");
            });

            $("#openFile").click(function () {
                $("#fileInput").click();
                $("#uploadOptions").addClass("hidden");
            });

            $("#openCamera").click(function () {
                $("#uploadOptions").addClass("hidden");

                navigator.mediaDevices.getUserMedia({
                    video: true
                })
                    .then(function (stream) {
                        let video = document.getElementById("cameraFeed");
                        video.srcObject = stream;
                        $("#cameraModal").removeClass("hidden");
                    })
                    .catch(function (error) {
                        console.error("Error accessing camera:", error);
                        message("Could not access the camera. Please check permissions.", 'error');
                    });
            });

            $("#captureImage").click(function () {
                let video = document.getElementById("cameraFeed");
                let canvas = document.createElement("canvas");
                canvas.width = video.videoWidth;
                canvas.height = video.videoHeight;
                let ctx = canvas.getContext("2d");
                ctx.drawImage(video, 0, 0, canvas.width, canvas.height);

                canvas.toBlob(function (blob) {
                    selectedFile = new File([blob], "captured_image.png", {
                        type: "image/png"
                    });
                    let imageUrl = URL.createObjectURL(selectedFile);
                    $("#filePreviewContainer").html(`
                <img src="${imageUrl}" class="border border-gray-300 rounded w-40 h-40 object-cover">
            `).removeClass("hidden");

                    let stream = video.srcObject;
                    if (stream) {
                        let tracks = stream.getTracks();
                        tracks.forEach(track => track.stop());
                    }

                    $("#cameraModal").addCclass("hidden");
                }, "image/png");
            });

            $("#closeCamera").click(function () {
                let video = document.getElementById("cameraFeed");
                let stream = video.srcObject;
                if (stream) {
                    let tracks = stream.getTracks();
                    tracks.forEach(track => track.stop());
                }
                $("#cameraModal").addClass("hidden");
            });

            $(document).click(function (event) {
                if (!$(event.target).closest("#uploadBtn, #uploadOptions").length) {
                    $("#uploadOptions").addClass("hidden");
                }
            });

            $("#fileInput").change(function (event) {
                let file = event.target.files[0];
                selectedFile = file;

                if (file) {
                    let fileType = file.type;
                    let reader = new FileReader();

                    reader.onload = function (e) {
                        let previewHtml = "";

                        if (fileType.startsWith("image/")) {
                            previewHtml = `
                    <div class="flex justify-end">
                        <div class="relative bg-gray-300 dark:bg-gray-700 p-2 rounded-lg max-w-xs">
                            <button class="removeFile absolute top-0 right-0 text-red-500 text-lg p-1">✖</button>
                            <img src="${e.target.result}" class="w-20 h-20 rounded-lg border">
                        </div>
                    </div>
                `;
                        } else if (fileType.startsWith("video/")) {
                            previewHtml = `
                    <div class="flex justify-end">
                        <div class="relative bg-gray-300 dark:bg-gray-700 p-2 rounded-lg max-w-xs">
                            <button class="removeFile absolute top-0 right-0 text-red-500 text-lg p-1">✖</button>
                            <video class="w-20 h-20 rounded-lg border" controls>
                                <source src="${e.target.result}" type="${fileType}">
                            </video>
                        </div>
                    </div>
                `;
                        } else {
                            previewHtml = `
                    <div class="flex justify-end">
                        <div class="flex items-center space-x-2 bg-gray-300 dark:bg-gray-700 p-2 rounded-lg max-w-xs text-sm">
                            <i class="bi bi-file-earmark-text text-gray-800 dark:text-gray-200 text-xl"></i>
                            <span class="text-gray-900 dark:text-gray-200 truncate max-w-[100px]">${file.name}</span>
                            <button class="removeFile text-red-500 text-lg">✖</button>
                        </div>
                    </div>
                `;
                        }

                        $("#filePreviewContainer").html(previewHtml).removeClass("hidden");
                    };

                    reader.readAsDataURL(file);
                }
            });

            $(document).on("click", ".removeFile", function () {
                $("#filePreviewContainer").html("").addClass("hidden");
                selectedFile = null;
            });



            $("#sendBtn").click(function () {
                let messageInput = $("#message").val().trim();

                if (!messageInput && !selectedFile) return;

                if (!receiverId) {
                    message("Please select a user or group to chat.", 'error');
                    return;
                }

                sendMessage(messageInput, selectedFile);
            });


            function sendMessage(message, file = null) {
                let formData = new FormData();
                formData.append("_token", "{{ csrf_token() }}");
                formData.append("receiver_id", receiverId);
                formData.append("message", message || "");

                if (file) {
                    formData.append("file", file);
                }

                $.ajax({
                    url: "{{ route('send.message') }}",
                    method: "POST",
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function (response) {
                        let messageContent = "";
                        let textMessage = response.message ? response.message.split(" || ")[0].trim() :
                            "";
                        let fileMessage = response.message && response.message.includes(" || ") ?
                            response.message.split(" || ")[1].trim() : "";

                        if (response.file_url) {
                            messageContent += `
                    <div class="mt-2">
                        ${displayMediaType(response.file_url, response.file_type, "", response.sender_name)}
                    </div>
                `;
                        }

                        if (textMessage !== "") {
                            messageContent += `<p class="text-sm mt-1">${textMessage}</p>`;
                        }

                        if (messageContent.trim() !== "") {
                            $("#nomessagetext").remove();
                            let messageHtml = `
                    <div class="flex flex-col items-end mb-2">
                        <div class="dark:bg-gray-700 text-white rounded-lg px-4 py-2 max-w-xs shadow-md">
                            <span class="font-semibold block">${response.sender_name}</span>
                            ${messageContent}
                        </div>
                        <span class="text-xs text-gray-500 dark:text-gray-400 mt-1">${response.time}</span>
                    </div>
                `;

                            $("#chat-box").append(messageHtml);
                        }

                        $("#message").val("");
                        $("#filePreviewContainer").html("").addClass("hidden");
                        selectedFile = null;
                        $("#chat-box").scrollTop($("#chat-box")[0].scrollHeight);
                    },
                    error: function (xhr, status, error) {
                        console.error("Error:", error);
                        console.log(xhr.responseText);
                        message("Failed to send message. Please try again.", 'error');
                    }
                });
            }


            $("#message").keydown(function (event) {
                if (event.key === "Enter") {
                    event.preventDefault();
                    $("#sendBtn").click();
                }
            });

            let chatId = null;
            let subscribedGroups = {};
            let subscribedUsers = {};

            $(".user-btn").click(function () {
                chatId = $(this).data("receiver-id");
                let type = $(this).data("type");

                $(this).find(".unread-count").remove();

                if (type === "group") {
                    subscribeToGroupChat(chatId);
                } else {
                    subscribeToPrivateChat(chatId);
                }
            });

            subscribeToNotifications(userId);

            function subscribeToGroupChat(chatId) {
                if (subscribedGroups[chatId]) return;
                subscribedGroups[chatId] = true;

                window.Echo.private(`group.chat.${chatId}`)
                    .listen('SendMessage', (data) => {
                        console.log("Group Chat Message Received:", data);
                        handleIncomingMessage(data, true);
                    });
            }

            function subscribeToPrivateChat(chatId) {
                if (subscribedUsers[chatId]) return;
                subscribedUsers[chatId] = true;

                window.Echo.private(`chat.${chatId}`)
                    .listen('SendMessage', (data) => {
                        console.log("Private Chat Message Received:", data);
                        handleIncomingMessage(data, false);
                    });
            }

            function subscribeToNotifications(userId) {
                window.Echo.private(`notifications.${userId}`)
                    .listen('MessageNotification', (data) => {
                        console.log("Notification Received:", data);

                        let userBtn = $(`.user-btn[data-receiver-id="${data.sender_id}"]`);

                        if (!chatId || chatId !== data.sender_id) {
                            if (userBtn.length) {
                                addUnreadCount(userBtn, data.unread_count || 1);
                            }
                            showNotification(data.notification);
                        }
                    });
            }

            function handleIncomingMessage(data, isGroup) {
                if (data.sender_id === userId) return;

                let receiverId = isGroup ? data.group_id : data.sender_id;
                let userBtn = $(`.user-btn[data-receiver-id="${receiverId}"]`);

                if (chatId !== receiverId) {
                    addUnreadCount(userBtn, data.unread_count || 1);
                } else {
                    appendMessage(data);
                    markMessagesAsRead(data.sender_id);
                }
            }

            function addUnreadCount(userBtn, unreadCount) {
                let unreadCountElem = userBtn.find(".unread-count");

                if (unreadCount <= 0) {
                    unreadCountElem.remove();
                    return;
                }

                if (unreadCountElem.length) {
                    unreadCountElem.text(unreadCount);
                } else {
                    userBtn.append(
                        `<span class="ml-auto bg-red-500 text-white text-xs font-semibold px-2 py-1 rounded-full unread-count">${unreadCount}</span>`
                    );
                }
            }

            function appendMessage(data) {
                $("#nomessagetext").remove();

                let messageContent = "";

                if (data.file_url) {
                    messageContent += `<div class="mt-2">${displayMediaType(data.file_url, data.file_type, data.message, data.sender_name)}</div>`;
                }

                if (data.message && (!data.file_url || data.message.includes(" || "))) {
                    let extracted = data.message.includes(" || ") ? data.message.split(" || ")[0] : data.message;
                    messageContent += `<p class="text-gray-900 dark:text-gray-200">${extracted}</p>`;
                }

                let messageHtml = `
        <div class="flex flex-col items-start mb-2">
            <div class="rounded-lg p-2 max-w-xs bg-gray-300 dark:bg-gray-700 text-gray-900 dark:text-gray-200">
                <p class="text-sm font-semibold">${data.sender_name}</p>
                ${messageContent}
            </div>
            <span class="text-xs text-gray-500 dark:text-gray-400 mt-1">${data.time}</span>
        </div>
    `;

                $("#chat-box").append(messageHtml).scrollTop($("#chat-box")[0].scrollHeight);
            }

            function markMessagesAsRead(senderId) {
                $.post("/mark-messages-as-read", {
                    sender_id: senderId,
                    _token: $('meta[name="csrf-token"]').attr("content")
                }).done(() => {
                    $(`.user-btn[data-receiver-id="${senderId}"] .unread-count`).remove();
                });
            }

            function showNotification(message) {
                let container = $("#notification-container");
                let notification = $("<div class='notification'></div>").text(message);

                container.append(notification);
                setTimeout(() => {
                    notification.addClass("fade-out");
                    setTimeout(() => notification.remove(), 300);
                }, 2000);
            }

            function message(msg, type) {
                const container = document.querySelector('.message-container');
                container.innerHTML = '';

                const div = document.createElement('div');
                div.textContent = msg;
                div.className = 'p-2 bg-green-500 text-white text-center rounded-lg mb-2';

                container.appendChild(div);

                setTimeout(() => {
                    container.innerHTML = '';
                }, 3000);
            }
        });
    </script>
</x-app-layout>