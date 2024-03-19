var articles = document.querySelectorAll(".add_cart_for_js");

const popupI = document.getElementById("popupInfo");

articles.forEach(art =>  {
    art.addEventListener("click", function(event) {
        event.preventDefault();

        hidePopUp();

        var xhr = new XMLHttpRequest();
        xhr.open("GET", event.target.getAttribute("href") , true);

        xhr.onreadystatechange = function() {
            if(xhr.readyState === 4 && xhr.status === 200) {
                console.log(xhr.responseText);

                popupI.style.display = "block";

                document.getElementById("popupContent").innerHTML = xhr.responseText;

                setTimeout(hidePopUp, 5000);
            }
        };

        xhr.send();
    });
});

function hidePopUp() {
    document.getElementById("popupInfo").style.display = "none";
}