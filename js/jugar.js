//Al hacer scroll:
document.addEventListener("scroll", () => {
	// Obtener la posición actual del scroll
	let pos = window.scrollY;
	//Almacenar la posición del scroll en una cookie
	document.cookie = "position=" + pos;
});

// Hace scroll automáticamente hasta el final de la página al cargarse
window.onload = function () {

    arrCookies = document.cookie.split(";");

	for (const key in arrCookies) {
		let aux = arrCookies[key].split("=");
		if (aux[0].trim() == "position") {
			pos = aux[1];
		}
	}

    window.scrollTo(0, pos);
};
