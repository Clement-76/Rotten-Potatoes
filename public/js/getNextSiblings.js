/**
 * return an array with the next siblings of an element
 * @param element the target element
 * @param addGivenElt if true the given element will be push in the array
 * @returns {Array}
 */
function getNextSiblings(element, addGivenElt = true) {
    let elements = [];

    if (addGivenElt) elements.push(element);

    let nextElt = element.nextElementSibling;

    while (nextElt !== null) {
        elements.push(nextElt);
        nextElt = nextElt.nextElementSibling;
    }

    return elements;
}

export default getNextSiblings;
