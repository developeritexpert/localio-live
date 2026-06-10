function toggleSelect() {
    document.querySelector(".custom-select").classList.toggle("active");
}

function selectOption(element) {
    document.getElementById("selected-option").innerText = element.innerText;
    document.querySelector(".custom-select").classList.remove("active");
}

document.addEventListener("DOMContentLoaded", function () {
    const menuToggler = document.querySelector(".menu-toggler");
    const dashboardLft = document.querySelector(".dashboard_lft");

    if (menuToggler && dashboardLft) {
        menuToggler.addEventListener("click", function () {
            menuToggler.classList.toggle("active"); // Toggle class on button
            dashboardLft.classList.toggle("menu-open"); // Toggle class on dashboard_lft
        });
    }
});

document.addEventListener("DOMContentLoaded", function () {
    document.querySelectorAll(".elps_icn").forEach((toggleButton) => {
        toggleButton.addEventListener("click", function (event) {
            event.stopPropagation(); // Prevents click event from bubbling up

            // Close all other dropdowns
            document.querySelectorAll(".dropdown-menu_review").forEach(menu => {
                if (menu !== this.nextElementSibling) {
                    menu.classList.remove("show");
                }
            });

            // Toggle only the corresponding dropdown
            const dropdownMenu = this.nextElementSibling;
            if (dropdownMenu && dropdownMenu.classList.contains("dropdown-menu_review")) {
                dropdownMenu.classList.toggle("show");
            }
        });
    });

    // Close the dropdown if clicking outside
    document.addEventListener("click", function (event) {
        document.querySelectorAll(".dropdown-menu_review").forEach(menu => {
            if (!menu.contains(event.target) && !event.target.classList.contains("elps_icn")) {
                menu.classList.remove("show");
            }
        });
    });
});


// image upload js
$(document).ready(function () {
    $('.upload-img').click(function () {
        $('#fileInput').click();
    });
    $('#fileInput').change(function (event) {
        var file = event.target.files[0];
        if (file) {
            console.log(file.name);
        }
    });
});

