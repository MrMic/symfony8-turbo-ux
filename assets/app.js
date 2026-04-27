import "@hotwired/turbo";
import "./stimulus_bootstrap.js";

Turbo.config.drive.progressBarDelay = 100; // ms avant affichage barre de progression

/*
 * Welcome to your app's main JavaScript file!
 *
 * This file will be included onto the page via the importmap() Twig function,
 * which should already be in your base.html.twig.
 */
import "./styles/app.css";
import "bootstrap";
import "bootstrap/dist/css/bootstrap.min.css";

console.log("This log comes from assets/app.js - welcome to AssetMapper! 🎉");
