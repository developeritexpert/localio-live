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




// sublist js /////////////////////////

document.addEventListener('DOMContentLoaded', function () {
    function addDropdownFunctionality(navContainer) {
        const navLinks = navContainer.querySelectorAll('.nav_sv');

        navLinks.forEach(navLink => {
            navLink.addEventListener('click', function (event) {
                event.preventDefault();

                const navItem = this.closest('.nav-links'); // Get closest parent
                const sublist = navItem.querySelector('.sublist'); // Get sublist inside the nav item
                const arrow = this.querySelector('.arrow i');

                if (!sublist) return;

                // Close all other dropdowns (except the one being clicked)
                document.querySelectorAll('.sublist').forEach(otherSublist => {
                    if (otherSublist !== sublist) {
                        otherSublist.classList.remove('show');
                        otherSublist.closest('.nav-links').querySelector('.arrow i')?.classList.remove('rotate');
                    }
                });

                // Toggle the clicked dropdown
                sublist.classList.toggle('show');
                arrow.classList.toggle('rotate');
            });
        });
    }

    // Apply functionality to existing navigation
    addDropdownFunctionality(document);
});

// page1 dropdown //////////////////

document.addEventListener("DOMContentLoaded", function () {
    const selectBox = document.querySelector(".select-box");
    const optionsContainer = document.querySelector(".options");
    const selectedText = document.querySelector(".selected");

    // Toggle dropdown
    selectBox.addEventListener("click", function () {
        optionsContainer.classList.toggle("show");
        selectBox.classList.toggle("open");
    });

    // Handle option selection
    document.querySelectorAll(".options div").forEach(option => {
        option.addEventListener("click", function () {
            selectedText.textContent = this.textContent;
            optionsContainer.classList.remove("show");
            selectBox.classList.remove("open");
        });
    });

    // Close dropdown when clicking outside
    document.addEventListener("click", function (event) {
        if (!selectBox.contains(event.target)) {
            optionsContainer.classList.remove("show");
            selectBox.classList.remove("open");
        }
    });
});




// page upload
document.getElementById("fileInput_img").addEventListener("change", function(event) {
    const file = event.target.files[0];
    const errorMsg = document.getElementById("errorMsg");
    const preview = document.getElementById("preview");

    if (file) {
        if (!file.type.includes("jpeg")) { 
            alert("only JPG and PNG images are allowed!");
            event.target.value = ""; // Clear the input
            preview.style.display = "none";
            return;
        }

        errorMsg.textContent = ""; // Clear error message
        const reader = new FileReader();
        reader.onload = function(e) {
            preview.src = e.target.result;
            preview.style.display = "block";
        };
        reader.readAsDataURL(file);
    }
});
document.getElementById("fileInput_video").addEventListener("change", function(event) {
    const file = event.target.files[0];
    const errorMsg = document.getElementById("errorMsg");
    const preview = document.getElementById("preview");

    if (file) {
        if (!file.type.includes("mp4")) { 
            alert("only Mp4 are allowed!");
            event.target.value = ""; // Clear the input
            preview.style.display = "none";
            return;
        }

        errorMsg.textContent = ""; // Clear error message
        const reader = new FileReader();
        reader.onload = function(e) {
            preview.src = e.target.result;
            preview.style.display = "block";
        };
        reader.readAsDataURL(file);
    }
});

  


// form js
document.addEventListener("DOMContentLoaded", function () {

    document.querySelectorAll(".options-s > input[type='text']").forEach(input => {

        input.addEventListener("click", function (event) {

            event.stopPropagation();



            // Remove 'open' class from all .options-s divs

            document.querySelectorAll(".options-s").forEach(div => div.classList.remove("open"));



            // Hide all dropdown lists

            document.querySelectorAll(".dropdown-list").forEach(list => list.classList.remove("show"));



            // Add 'open' class only to the parent .options-s of the clicked input

            const parentDiv = this.closest(".options-s");

            if (parentDiv) {

                parentDiv.classList.add("open");

            }



            // Show the corresponding dropdown

            const dropdownList = this.nextElementSibling;

            if (dropdownList) {

                dropdownList.classList.add("show");

            }

        });



        // Handle dropdown option selection

        input.nextElementSibling.addEventListener("click", function (event) {

            if (event.target.dataset.value) {

                input.value = event.target.textContent;

                input.classList.add("selected-option");



                // Remove 'open' class from the parent div

                const parentDiv = input.closest(".options-s");

                if (parentDiv) {

                    parentDiv.classList.remove("open");

                }



                // Hide the dropdown

                this.classList.remove("show");

            }

        });

    });



    // Click outside to close all dropdowns

    document.addEventListener("click", function () {

        document.querySelectorAll(".options-s").forEach(div => div.classList.remove("open"));

        document.querySelectorAll(".dropdown-list").forEach(list => list.classList.remove("show"));

    });

});

