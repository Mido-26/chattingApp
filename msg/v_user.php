<?php
session_start();
if (isset($_SESSION["id"])) {
    $id = htmlspecialchars($_SESSION["id"]);
    $name = htmlspecialchars($_SESSION['username']);
    $avatar = !empty($_SESSION['avatar']) ? htmlspecialchars($_SESSION['avatar']) : 'default.jpg';

?>
    <!DOCTYPE html>
    <html lang="en">

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>View Users</title>
        <link rel="stylesheet" href="../css/style.css">
        <link rel="stylesheet" href="../css/all.min.css">
        <link rel="stylesheet" href="style.css">
    </head>

    <body>
        <div class="container">
            <div class="main-content">
                <div class="users">
                    <div class="info-user">
                        <div class="name-info">
                            <img src="../upload/<?php echo $avatar; ?>">
                            <h3 class="name-a"><?php echo $name; ?></h3>
                        </div>
                        <div class="icons">
                            <i class="fa fa-cog"></i>
                            <i class="fa fa-sign-out-alt d" id="logout"></i>
                        </div>
                    </div>
                    <div class="a_user">
                        <div class="search-bar">
                            <i class="fa fa-search"></i>
                            <input type="text" placeholder="Search friend or start chatting " minlength="3" id="search-input">
                        </div>
                    </div>
                    <hr>

                    <div class="cont"></div>
                    <div class="search-user"></div>
                    <div class="info">
                        <p class="infop">No Friends, search friends to chat</p>
                    </div>
                </div>
                <div class="msg-cont">
                    <div class="msg-chat">
                        <header class="chat-head">

                        </header>
                        <main class="chat-body">
                            <div class="loading">
                                <img src="../assets/loader2.gif">
                                <p>loading Chats</p>
                            </div>
                            <div class="msg-info">
                                <p></p>
                            </div>
                        </main>
                        <footer class="chat-sub">
                            <button type="button" id="attachBtn"><i class="fas fa-paperclip"></i></button>
                            <div class="sub-cont">
                                <textarea name="msg" placeholder="Send a message..." id="msg" cols="30" rows="1"></textarea>
                            </div>
                            <button type="button" id="sendBtn" disabled><i class="fa fa-paper-plane"></i></button>
                        </footer>
                    </div>
                </div>
            </div>
        </div>

        <template id="friends">
            <div class="content flex" id="">
                <div class="profile flex-col">
                    <img src="../upload/default.jpg" alt="Avatar">
                </div>
                <div class="userinfo flex-col">
                    <h4 class="name"></h4>
                    <p class="msg"></p>
                </div>
                <div class="status flex-col">
                    <p></p>
                    <span></span>
                </div>
            </div>
        </template>
        <template id="messag">
            <div class="day">
                <p>wednesday</p>
            </div>
            <div class="receiver">
                <p class="text">
                    hi
                    <span>11:04 AM</span>
                </p>
            </div>
            <div class="sender">
                <p class="text">
                    hello
                    <span>11:04 AM</span>
                </p>
            </div>
        </template>
        <template id="u_infomat">
            <img src="#" class="profile-img">
            <h3 class="chat-name" id="name"></h3>
        </template>
        <script src="jquery-3.7.1.js"></script>
        <script src="../js/script.js"></script>
        <script src="script.js"></script>
        <script src="../js/view_data.js"></script>
        <script src="../js/view.js"></script>
    </body>

    </html>
<?php
} else {
    header("Location: ../index.html");
    exit();
}
?>