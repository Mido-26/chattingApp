$(document).ready(function () {
    let lastUpdated = null;

    async function getFriends(initialLoad = false) {
        try {
            const endpoint = "../php/getFriend.php";
            const requestOptions = {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({ lastUpdated: lastUpdated })
            };

            let response = await fetch(endpoint, requestOptions);
            let res = await response.json();
            $('.info').hide();

            if (res.status == 'success' && res.friends) {
                let content = $('.cont');
                let friends = res.friends;

                // Sort friends by time (most recent first)
                friends.sort((a, b) => new Date(a.time) - new Date(b.time));

                const template = document.getElementById('friends').content;

                friends.forEach(friend => {
                    const existingFriend = $(`#${friend.id}`);

                    if (existingFriend.length > 0) {
                        // Update existing friend's details
                        existingFriend.find('.profile img').attr('src', friend.avatar ? `../upload/${friend.avatar}` : '../upload/default.jpg');
                        existingFriend.find('.name').text(friend.username);
                        existingFriend.find('.msg').text(friend.lastmsg);

                        const timeElement = existingFriend.find('.status p');
                        const time = new Date(friend.time);
                        const now = new Date();

                        const isToday = time.toDateString() === now.toDateString();
                        const isYesterday = time.toDateString() === new Date(now.setDate(now.getDate() - 1)).toDateString();

                        if (isToday) {
                            timeElement.text(time.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' }));
                        } else if (isYesterday) {
                            timeElement.text('Yesterday');
                        } else {
                            timeElement.text(time.toLocaleDateString('en-GB', { day: '2-digit', month: '2-digit', year: '2-digit' }));
                        }

                        // Move existing friend to the top with animation
                        existingFriend.prependTo(content);
                        // setTimeout(() => {
                        //     existingFriend.removeClass('move-up');
                        // }, 500);
                    } else {
                        // Add new friend's details
                        const clone = document.importNode(template, true);
                        const contentDiv = clone.querySelector('.content');
                        contentDiv.id = `${friend.id}`; // Set the ID dynamically
                        clone.querySelector('.profile img').src = friend.avatar ? `../upload/${friend.avatar}` : '../upload/default.jpg';
                        clone.querySelector('.name').textContent = friend.username;
                        clone.querySelector('.msg').textContent = friend.lastmsg;

                        const timeElement = clone.querySelector('.status p');
                        const time = new Date(friend.time);
                        const now = new Date();

                        const isToday = time.toDateString() === now.toDateString();
                        const isYesterday = time.toDateString() === new Date(now.setDate(now.getDate() - 1)).toDateString();

                        if (isToday) {
                            timeElement.textContent = time.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
                        } else if (isYesterday) {
                            timeElement.textContent = 'Yesterday';
                        } else {
                            timeElement.textContent = time.toLocaleDateString('en-GB', { day: '2-digit', month: '2-digit', year: '2-digit' });
                        }

                        // Append new friend and animate
                        content.prepend(clone);
                    }
                });

                // Update lastUpdated timestamp
                lastUpdated = res.lastUpdated;
            } else {
                if (initialLoad) {
                    $('.info').show();
                }
                $('.infop').text(res.msg);
            }
        } catch (error) {
            console.error("Fetch error: ", error);
            if (initialLoad) {
                $('.info').show();
            }
            $('.infop').text("An error occurred while fetching data.");
        }
    }

    getFriends(true);

    setInterval(getFriends, 3000); // Changed interval to 3 seconds

    $('#clear-search').on('click', function() {
        $('#search-input').val('');
        $('#search-input').focus();
    });
});
