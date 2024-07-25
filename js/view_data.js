$(document).ready(function () {
    const activeFriendId = getCookie('selectedFriendId');
    async function addActiveClass() {
        const $element = $('#' + activeFriendId);
        if ($element.length) {
            $element.addClass('active');
            getMsg(activeFriendId);
            return true;
        } else {
            return false;
        }
    }

    async function retryUntilSuccess() {
        while (true) {
            const success = await addActiveClass();
            if (success) {
                break;
            } else {
                await new Promise(resolve => setTimeout(resolve, 1000));
            }
        }
    }

    if (activeFriendId) {
        retryUntilSuccess();
    }

    $('#logout').on('click', function () {
        window.location.href = '../php/logout.php';
    });

    $('.cont').on('click', '.content', function () {
        let c_id = $(this).attr('id');
        $('.content').removeClass('active');
        $(this).addClass('active');
        $('.loading').show();
        setCookie('selectedFriendId', c_id, 10);
        // activeFriendId = getCookie('selectedFriendId')
        getMsg(c_id);
    });

    $('.search-user').on('click', '.content', function () {
        let c_id = $(this).attr('id');
        $('.content').removeClass('active');
        $(this).addClass('active');
        $('.loading').show();
        console.log(c_id);
        setCookie('selectedFriendId', c_id, 10);
        // activeFriendId = getCookie('selectedFriendId')
        getMsg(c_id);
    });
    function setCookie(name, value, days) {
        var expires = "";
        if (days) {
            var date = new Date();
            date.setTime(date.getTime() + (days * 24 * 60 * 60 * 1000));
            expires = "; expires=" + date.toUTCString();
        }
        document.cookie = name + "=" + (value || "") + expires + "; path=/";
    }

    function getCookie(name) {
        var nameEQ = name + "=";
        var ca = document.cookie.split(';');
        for (var i = 0; i < ca.length; i++) {
            var c = ca[i];
            while (c.charAt(0) === ' ') c = c.substring(1, c.length);
            if (c.indexOf(nameEQ) === 0) return c.substring(nameEQ.length, c.length);
        }
        return null;
    }

    async function getMsg(id) {
        $.ajax({
            type: "post",
            url: "../php/getmsg.php",
            data: {
                rid: id
            },
            success: function (response) {
                let res = JSON.parse(response);
                $('.loading').hide();
                $('.msg-info').hide();
                if (res.status === 'success' && res.messages) {

                    const msgbody = $('.chat-body');
                    const u_i_body = $('.chat-head');

                    if (res.messages.length > 0) {
                        displayMessages(res.messages, id);
                        u_i_body.empty();
                        let u_info = res.u_info;
                        const template = document.getElementById('u_infomat').content;
                        const clone = document.importNode(template, true);
                        clone.querySelector('img').src = u_info.avatar ? `../upload/${u_info.avatar}` : '../upload/default.jpg';
                        clone.querySelector('.chat-name').textContent = u_info.username;
                        u_i_body.append(clone);
                        msgbody.scrollTop(msgbody.prop("scrollHeight"));
                    } else {
                        // $('.loading').hide();
                        $('.msg-info').show();
                        $('.msg-info p').$text('Start Chatting with your friend');
                        const messageContainer = document.querySelector('.chat-body');
                        messageContainer.innerHTML = '';
                    }
                } else {
                    // console.log('error25');
                    $('.loading').show();
                    $('.msg-info').show()
                    $('.msg-info p').text('network error Try again');
                    const messageContainer = document.querySelector('.chat-body');
                    messageContainer.innerHTML = '';
                }
            }
        });
    }
    // setInterval(() => { getMsg(activeFriendId) }, 1000);
    function displayMessages(messages, id) {
        const template = document.querySelector('#messag');
        const messageContainer = document.querySelector('.chat-body');
        let currentDay = '';
        messageContainer.innerHTML = '';

        messages.forEach(msg => {
            const msgDate = new Date(msg.date);
            const today = new Date();
            const yesterday = new Date(today);
            yesterday.setDate(today.getDate() - 1);

            let day;
            if (msgDate.toDateString() === today.toDateString()) {
                day = "Today";
            } else if (msgDate.toDateString() === yesterday.toDateString()) {
                day = "Yesterday";
            } else {
                day = msgDate.toLocaleDateString('en-US', {
                    weekday: 'long'
                });
            }

            const time = msgDate.toLocaleTimeString('en-US', {
                hour: 'numeric',
                minute: 'numeric',
                hour12: true
            });

            // Check if we need to display a new day
            if (day !== currentDay) {
                currentDay = day;
                const dayClone = template.content.querySelector('.day').cloneNode(true);
                dayClone.querySelector('p').textContent = currentDay;
                messageContainer.appendChild(dayClone);
            }

            const messageClone = msg.From == id ?
                template.content.querySelector('.receiver').cloneNode(true) :
                template.content.querySelector('.sender').cloneNode(true);

            messageClone.querySelector('.text').childNodes[0].nodeValue = msg.message;
            messageClone.querySelector('.text span').textContent = time;
            messageContainer.appendChild(messageClone);
        });
    }

    $('#sendBtn').click(function () {
        const id = getCookie('selectedFriendId');
        const message = $('#msg').val().trim();
        if (message) {
            const template = document.querySelector('#messag');
            const messageContainer = document.querySelector('.chat-body');
            const msgDate = new Date();
            const today = new Date();
            const yesterday = new Date(today);
            yesterday.setDate(today.getDate() - 1);

            let day;
            if (msgDate.toDateString() === today.toDateString()) {
                day = "Today";
            } else if (msgDate.toDateString() === yesterday.toDateString()) {
                day = "Yesterday";
            } else {
                day = msgDate.toLocaleDateString('en-US', {
                    weekday: 'long'
                });
            }

            const time = msgDate.toLocaleTimeString('en-US', {
                hour: 'numeric',
                minute: 'numeric',
                hour12: true
            });

            // const lastDayElement = messageContainer.querySelector('.day:last-of-type');
            // if (!lastDayElement || lastDayElement.querySelector('p').textContent !== day) {
            //     const dayClone = template.content.querySelector('.day').cloneNode(true);
            //     dayClone.querySelector('p').textContent = day;
            //     messageContainer.appendChild(dayClone);
            //     console.log('Added new day header:', day);
            // } else {
            //     console.log('Day header already exists:', day);
            // }
            // Clone the sender template
            const messageClone = template.content.querySelector('.sender').cloneNode(true);

            // Update the cloned template with the new message and time
            messageClone.querySelector('.text').childNodes[0].nodeValue = message;
            messageClone.querySelector('.text span').textContent = time;

            // Append the new message to the chat body
            messageContainer.appendChild(messageClone);

            // Clear the input field and reset its height
            $('#msg').val('');
            $('#msg').css('height', 'auto');
            $('#sendBtn').attr('disabled', 'disabled');

            // Scroll to the bottom
            $('.chat-body').scrollTop($('.chat-body')[0].scrollHeight);

            // Send the message via AJAX
            sendMsg(id, message);

        }
    });

    $('#msg').on('input', function () {
        const message = $(this).val().trim();
        if (message) {
            $('#sendBtn').removeAttr('disabled');
        } else {
            $('#sendBtn').attr('disabled', 'disabled');
        }
    });

    function sendMsg(id, message) {
        $.ajax({
            type: "post",
            url: "../php/sendmsg.php",
            data: ({ id, message }),
            success: function (response) {
                let res = JSON.parse(response);
                console.log(res)
                getMsg(id);
            }
        });
    }
});