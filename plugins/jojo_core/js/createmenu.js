var exists2;
var allStretch2;
var dark= "<img class=\"bg_submenu\" src=\"custom/images/bottom_left_navi.gif\" width=\"184\" border=\"no\" alt=\"\" />";
var light= "<img class=\"bg_submenu\" src=\"custom/images/bottom_left_navi_light.gif\" width=\"184\" border=\"no\" alt=\"\" />";

	//the main function, call to the effect object
function init2() {
		var divs2 = document.getElementsByClassName("stretcher");
		allStretch2 = new fx.MultiFadeSize(divs2, {duration: 400});

		items = document.getElementsByClassName("display");
		for (i = 0; i < items.length; i++){
			var span = items[i];
			div = span.nextSibling;
			span.title = span.className.replace("display ", "");

			if (window.location.href.indexOf(span.title) < 0) {
				allStretch2.hide(div, 'height');
				if (exists2 != true) exists2 = false;
			} else {
			 	exists2 = true;
					if (i == items.length-1){
					document.getElementById('img_navi_bottom').innerHTML = light;
					}
			}

			if (i == items.length-1){

				span.onclick = function(){
					if (exists2 == false) {
				  		this.nextSibling.fs.toggle('height');
				   		exists2 = true;
					}
					allStretch2.showThisHideOpen(this.nextSibling, 100, 'height');

					setTimeout("document.getElementById('img_navi_bottom').innerHTML = light",180);



				}
			}else{

				span.onclick = function(){
					if (exists2 == false) {
				  		this.nextSibling.fs.toggle('height');
				   		exists2 = true;
					}
					allStretch2.showThisHideOpen(this.nextSibling, 100, 'height');

					setTimeout("document.getElementById('img_navi_bottom').innerHTML = dark",250);


				}

			}
		}

}