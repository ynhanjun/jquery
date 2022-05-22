function createEle(tagName,styleOptions){
    var newEle = document.createElement(tagName);
    for(var key in styleOptions){
        newEle.style[key] = styleOptions[key];
    }
    return newEle;
}