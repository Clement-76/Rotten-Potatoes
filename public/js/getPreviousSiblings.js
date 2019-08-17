/**
 * return an array with the previous siblings of an element
 * @param element the target element
 * @param addGivenElt if true the given element will be push in the array
 * @returns {Array}
 */
function getPreviousSiblings(element, addGivenElt = true) {
    let elements = [];

    if (addGivenElt) elements.push(element);

    let nextElt = element.previousElementSibling;

    while (nextElt !== null) {
        elements.push(nextElt);
        nextElt = nextElt.previousElementSibling;
    }

    return elements;
}

export default getPreviousSiblings;
