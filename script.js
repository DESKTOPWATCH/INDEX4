// --- GESTION DU DIAPORAMA ---
let slideIndex = 0;
const slides = document.querySelectorAll('.slide');
const dots = document.querySelectorAll('.dot');

function showSlide(n) {
    if (slides.length === 0) return; // Évite les erreurs sur les pages sans diaporama
    
    if (n >= slides.length) slideIndex = 0;
    if (n < 0) slideIndex = slides.length - 1;

    slides.forEach(slide => slide.classList.remove('active'));
    dots.forEach(dot => dot.classList.remove('active'));

    slides[slideIndex].classList.add('active');
    dots[slideIndex].classList.add('active');
}

function changeSlide(n) {
    slideIndex += n;
    showSlide(slideIndex);
}

function currentSlide(n) {
    slideIndex = n;
    showSlide(slideIndex);
}

// Défilement automatique toutes les 5 secondes
setInterval(() => {
    changeSlide(1);
}, 5000);

// --- GESTION DES PROPOSITIONS (FOOTER) ---
function envoyerProposition(event) {
    event.preventDefault();
    alert("Merci pour votre proposition ! Notre équipe commerciale l'a bien reçue.");
    event.target.reset();
}

// --- GESTION DE LA COMMANDE ET DU PAIEMENT ---
function calculerTotal() {
    const prix = document.getElementById('productSelect').value;
    const quantite = document.getElementById('quantity').value;
    document.getElementById('totalAmount').innerText = prix * quantite;
}

function afficherChampsPaiement() {
    const methode = document.getElementById('paymentMethod').value;
    document.getElementById('mobileFields').style.display = (methode === 'mobile') ? 'block' : 'none';
    document.getElementById('cardFields').style.display = (methode === 'carte') ? 'block' : 

'none';
}

function traiterPaiement(event) {
    event.preventDefault();
    const nom = document.getElementById('clientName').value;
    const total = document.getElementById('totalAmount').innerText;
    const statusBox = document.getElementById('paymentStatus');

    statusBox.style.display = 'block';
    statusBox.innerHTML = `
        <strong>Requête envoyée !</strong><br>
        Bonjour ${nom}, une demande de transfert de <strong>${total} $</strong> vers le compte marchand de <em>KinStyle & Beauté</em> a été initiée. Veuillez valider avec votre code PIN sur votre téléphone ou banque.
    `;
}