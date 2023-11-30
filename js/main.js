document.addEventListener('DOMContentLoaded', function () {
  // date du copyright

  const date = new Date();
  const year = date.getFullYear();
  document.getElementById('year').innerHTML = year;

  // Slider
  $('.carousel').carousel({
    interval: 5000,
  });

  // API MAP

  // Creation map
  let map = L.map('map').setView([46.7625511, -1.9585355], 10);

  // creation Calque images
  L.tileLayer('https://{s}.tile.openstreetmap.fr/osmfr/{z}/{x}/{y}.png', {
    attribution:
      'données © <a href="//osm.org/copyright">OpenStreetMap</a>/ODbL - rendu <a href="//openstreetmap.fr">OSM France</a>',
    maxZoom: 18,
    accessToken:
      'secret',
  }).addTo(map);

  // ajout d'un marqeur

  let marker = L.marker([46.7625511, -1.9585355]).addTo(map);
  marker.on('click', () => {
    map = map.setView([46.7625511, -1.9585355], 10);
  });

  // form contact

  /**
   * Essaie de convertir la réponse retournée par le serveur en JSON.
   * Retourne une erreur si cela n'est pas possible, ou l'objet JSON si la réponse
   * a pu être convertie.
   * @param res Un objet Response. (https://developer.mozilla.org/fr/docs/Web/API/Response)
   * @returns {any} Un objet résultant d'une conversion JSON -> objet.
   */
  const gererReponseFetch = (res) => {
    // On vérifie si le code HTTP de la réponse est 200 (Texte correspondant: OK),
    // pour savoir si on peut traiter la réponse.
    if (res.ok) {
      try {
        console.log(res.body.text);
        // On essaie de convertir la réponse en JSON.
        // Si cela fonctionne, on quitte la fonction en retournant
        // les données de la réponse sous la forme d'un objet.
        return res.json();
      } catch (exception) {
        // Erreur: on affiche une erreur attrapable (catch) avec les détails.
        throw new Error(
          `Impossible de traiter la réponse serveur: conversion en JSON impossible. Details: ${exception}`
        );
      }
    } else {
      // Réponse avec code HTTP différent de 200 (généralement 400/500):
      // Il y a des chances que ce soit une erreur !
      throw new Error(
        'Impossible de traiter la réponse serveur: Réponse erronée.'
      );
    }
  };

  /**
   * Traite la réponse JSON retournée par le serveur.
   * @param json Un objet résultant de la conversion de la réponse en JSON.
   */
  const gererReponseContactForm = (json) => {
    afficherPopup(json.message, json.result_code ? 'red' : 'green');
    if (!json.result_code) {
      // Vider le formulaire si la requête a fonctionné.
      $('#contact_form')
        .find('input[type=text], input[type=email], textarea')
        .val('');
    }
  };

  const afficherPopup = (message, couleur) => {
    const popup = $('#popup_contact');
    popup.css('background', couleur);
    popup.html(`<p>${message}</p>`);
    popup.fadeTo(500, 0.85);
    setTimeout(() => popup.fadeTo(500, 0, () => popup.hide()), 5000);
  };

  const envoyerFormulaire = (langue) => {
    let formData = new FormData(document.getElementById('contact_form'));
    formData.append('lang', langue);
    fetch('/contactForm', {
      method: 'POST',
      body: formData,
    })
      .then(gererReponseFetch)
      .then(gererReponseContactForm)
      .catch((err) => {
        console.error(err);
        afficherPopup("Une erreur s'est produite.", 'red');
      });
  };

  document.getElementById('contact_form').addEventListener('submit', (evt) => {
    evt.preventDefault();
    envoyerFormulaire(evt.target.dataset.lang);
  });
});
