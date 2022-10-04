<?php
    function is_post_request()
    {
        return strtoupper($_SERVER['REQUEST_METHOD']) === 'POST';
    }

    function is_get_request()
    {
        return strtoupper($_SERVER['REQUEST_METHOD']) === 'GET';
    }

    function format_difficulty($difficulty) {
        $fmt_diff_string = "";
        for ($x = 1; $x <= $difficulty; $x++) {
            $fmt_diff_string .= "⭐";
        }
        return $fmt_diff_string;
    }
    
    function create_modal($message) {
        echo('<div id="myModal" class="modal"><div class="modal-content">'.$message.'<center><input class="close" value="Close"></center></div></div>');
        echo('
        <script>
            // Get the modal
            var modal = document.getElementById("myModal");

            // Get the button that opens the modal
            var btn = document.getElementById("myBtn");

            // Get the <span> element that closes the modal
            var span = document.getElementsByClassName("close")[0];
            var home = document.getElementsByClassName("home")[0];

            // Open the modal 
            modal.style.display = "block";

            // When the user clicks on <span> (x), close the modal
            span.onclick = function() {
                modal.style.display = "none";
            }

            home.onclick = function() {
                location.assign("/");
            }

            // When the user clicks anywhere outside of the modal, close it
            window.onclick = function(event) {
            if (event.target == modal) {
                modal.style.display = "none";
            }
            }
        </script>
        ');
    }
?>