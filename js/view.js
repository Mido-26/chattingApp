$(function () {
    let data = [];

    function debounce(func, delay) {
        let timeout;
        return function (...args) {
            clearTimeout(timeout);
            timeout = setTimeout(() => func.apply(this, args), delay);
        };
    }

    function fetchData() {
        $.ajax({
            type: "get",
            url: "../php/getuser.php",
            success: function (res) {
                // console.log("AJAX Response:", res);  // Debug
                res = JSON.parse(res);

                // Assuming res.users is an array of objects with a 'username' property
                if (Array.isArray(res.users)) {
                    data = res.users.filter(user => typeof user.username === 'string');
                } else {
                    console.error("Unexpected data format:", res);
                }

                // console.log("Data:", data);  // Debug
            },
            error: function (err) {
                // console.error("AJAX Error:", err);  // Debug
            }
        });
    }

    fetchData();

    $('#search-input').on('input', debounce(function () {
        const s_input = $(this).val().trim().toLowerCase();

        if ($(this).val().length > 0) {
            $('.cont').hide();
            $('.search-user').show();

        } else {
            $('.cont').show();
            $('.search-user').hide();
        }

        console.log("Search Input:", s_input);  // Debug

        var s_data = data.filter(user => user.username.toLowerCase().includes(s_input));
        console.log("Filtered Data:", s_data);

        $('.search-user').empty();
        let content = $('.search-user');
        let friends = s_data;
        friends.forEach(friend => {
            const template = document.getElementById('friends').content;
            const clone = document.importNode(template, true);
            const contentDiv = clone.querySelector('.content');
            contentDiv.id = `${friend.id}`; // Set the ID dynamically
            console.log(`${friend.id}`);
            clone.querySelector('.profile img').src = friend.avatar ? `../upload/${friend.avatar}` : '../upload/default.jpg';
            clone.querySelector('.name').textContent = friend.username;
            // Append new friend and animate
            content.append(clone);
            // Debug
        });
    }, 300));
});
