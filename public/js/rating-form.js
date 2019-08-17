import getPreviousSiblings from "./getPreviousSiblings.js";
import getNextSiblings from "./getNextSiblings.js";

let stars = document.querySelectorAll('#rating-form .star');
let ratingInput = document.getElementById('rating_notation');

stars.forEach(star => {

    star.addEventListener('mouseover', function() {
        getPreviousSiblings(this).forEach(elt => elt.classList.replace('far', 'fas'));
        getNextSiblings(this, false).forEach(elt => elt.classList.replace('fas', 'far'));
    });

    star.addEventListener('mouseout', function() {
        // if the user didn't select a value
        if (ratingInput.value == 0) {
            stars.forEach(elt => elt.classList.replace('fas', 'far'));
        } else {
            console.log(stars);
            [...stars].slice(0, ratingInput.value).forEach(star => {
                getPreviousSiblings(star).forEach(elt => elt.classList.replace('far', 'fas'));
                getNextSiblings(star, false).forEach(elt => elt.classList.replace('fas', 'far'));
            });
        }
    });

    star.addEventListener('click', function() {
        ratingInput.value = this.dataset.value;
    });
});
