function insertAfter(newNode, referenceNode) {
    referenceNode.parentNode.insertBefore(newNode, referenceNode.nextSibling);
}

function convertCanvasToImage(canvas) {
	var image = new Image();
	image.src = canvas.toDataURL("image/png");
	return image;
}

function fadeOutEffect(target) {
    var fadeEffect = setInterval(function () {
        if (!target.style.opacity) {
            target.style.opacity = 1;
        }
        if (target.style.opacity < 0.2) {
            clearInterval(fadeEffect);
        } else {
            target.style.opacity -= 0.2;
        if (target.style.opacity == 0)
			target.parentNode.removeChild(target);
        }
    }, 200);
}

document.addEventListener('DOMContentLoaded', function ()
{
	var div_message = document.getElementsByClassName('message');

	for (var i = 0; i < div_message.length; i++) {
		div_message[i].onclick = function() {
			fadeOutEffect(this);
		}
	}

}, false );