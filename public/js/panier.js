const inputs = document.querySelectorAll('input.qty');

var form = document.getElementById("panierForm");

inputs.forEach(input =>  {
    input.addEventListener('input', function(event) {
        var id = event.target.id;

        var quant = event.target.value;

        var nbIdentification = id.substring(id.indexOf("_") + 1);

        if(quant == 0) {

            document.getElementById("remove_" + nbIdentification).click();

        } else {

            var amountUElem = document.getElementById("amount_u_" + nbIdentification);

            var amountUString = amountUElem.innerHTML.substring(0, amountUElem.innerHTML.length - 1);// enleve le �

            var prixU = parseFloat(amountUString.replace(",", "."));

            var totalPrixArt = document.getElementById("amount_" + nbIdentification);

            totalPrixArt.innerHTML = (prixU * quant).toFixed(2) + " \u20AC";

            var formData = new FormData(form);
            var xhr = new XMLHttpRequest();
            xhr.open("POST", form.action, true);
            xhr.onreadystatechange = function() {
                if(xhr.readyState === 4 && xhr.status === 200) {
                    console.log("recalculer");
                }
            };
            xhr.send(formData);
        }

        updatePrixTotal();
    });
});

function updatePrixTotal() {
    var ttscElem = document.getElementById("totalSansCharge");

    var ttacElem = document.getElementById("totalAvecCharge");

    const arts = document.querySelectorAll('span.total_ligne');

    var ttsc = 0;

    var livraison = 0;

    arts.forEach(art =>  {

        var amountUString = art.innerHTML.substring(0, art.innerHTML.length - 1);// enleve le �

        var prixAr = parseFloat(amountUString.replace(",", "."));

        ttsc += prixAr;
    });

    ttsc = ttsc.toFixed(2);

    ttscElem.innerHTML = ttsc + " \u20AC";
    ttacElem.innerHTML = ((ttsc + livraison) * 1.0).toFixed(2) + " \u20AC";
}


const removes = document.querySelectorAll("a.remove");

removes.forEach(remove =>  {
    remove.addEventListener("click", function(event) {

        var id = event.target.id;

        var quant = event.target.value;

        var nbIdentification = id.substring(id.indexOf("_") + 1);

        document.getElementById("popupOui").setAttribute('onClick', 'window.location = "/supprimerLigne?id=' + nbIdentification + '"');

        document.getElementById("popupNon").addEventListener("click", function(event) {
            const nbIdentificationBc = nbIdentification;
            document.getElementById("quantity_" + nbIdentificationBc).value = 1;

            hidePopUp();
        });

        document.getElementById("popup").style.display = "block";
    });
});

function hidePopUp() {
    document.getElementById("popup").style.display = "none";
}