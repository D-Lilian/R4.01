const inputs = document.querySelectorAll('input.qty');

inputs.forEach(input =>  {
    input.addEventListener('input', function(event) {
        var id = event.target.id;

        var quant = event.target.value;

        var nbIdentification = id.substring(id.indexOf("_")+1);

        var amountUElem = document.getElementById("amount_u_"+nbIdentification);

        var amountUString = amountUElem.innerHTML.substring(0, amountUElem.innerHTML.length - 1); // enleve le €

        var prixU = parseFloat(amountUString.replace(",", "."));

        var totalPrixArt = document.getElementById("amount_" + nbIdentification);

        totalPrixArt.innerHTML = (prixU * quant).toFixed(2) + " \u20AC";



        updatePrixTotal();
    });
});

const form = document.getElementById("validerPanier");

form.addEventListener("click", function () {
    document.getElementById("panierForm").submit();
});

function updatePrixTotal() {
    var ttscElem = document.getElementById("totalSansCharge");

    var ttacElem = document.getElementById("totalAvecCharge");

    const arts = document.querySelectorAll('span.total_ligne');

    var ttsc = 0;

    var livraison = 0;

    arts.forEach(art => {

        var amountUString = art.innerHTML.substring(0, art.innerHTML.length - 1); // enleve le €

        var prixAr = parseFloat(amountUString.replace(",", "."));

        ttsc += prixAr;
    });

    ttsc = ttsc.toFixed(2);

    ttscElem.innerHTML = ttsc + " \u20AC";
    ttacElem.innerHTML = (ttsc + livraison).toFixed(2) + " \u20AC";
}

