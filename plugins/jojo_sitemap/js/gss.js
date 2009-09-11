/* Google Sitmaps Stylesheets (GSStylesheets) JavaScript
   License http://www.gnu.org/copyleft/lesser.html GNU/LGPL

   Created by Johannes Mueller (http://gsitecrawler.com)
   1.0 / 21 Aug 2005 - based on ideas by "ameba" / via experts-exchange

   Changes by Serge Baccou
   1.1 / 21 Aug 2005 - use of the same JavaScript for siteindex
   1.2 / 24 Aug 2005 - sorting for siteindex */

			var selectedColor = "blue";
			var defaultColor = "black";
			var hdrRows = 1;
			var numeric = '..';
			var desc = '..';
			var html = '..';
			var freq = '..';

			function initXsl(tabName,fileType) {
				hdrRows = 1;

			  if(fileType=="sitemap") {
			  	numeric = ".3.";
			  	desc = ".1.";
			  	html = ".0.";
			  	freq = ".2.";
			  	initTable(tabName);
				  setSort(tabName, 3, 1);
			  }
			  else {
			  	desc = ".1.";
			  	html = ".0.";
			  	initTable(tabName);
				  setSort(tabName, 1, 1);
			  }

				var theURL = document.getElementById("head1");
				theURL.innerHTML += ' ' + location;
				document.title += ': ' + location;
			}

			function initTable(tabName) {
			  var theTab = document.getElementById(tabName);
			  for(r=0;r<hdrRows;r++)
			   for(c=0;c<theTab.rows[r].cells.length;c++)
			     if((r+theTab.rows[r].cells[c].rowSpan)>hdrRows)
			       hdrRows=r+theTab.rows[r].cells[c].rowSpan;
			  for(r=0;r<hdrRows; r++){
			    colNum = 0;
			    for(c=0;c<theTab.rows[r].cells.length;c++, colNum++){
			      if(theTab.rows[r].cells[c].colSpan<2){
			        theCell = theTab.rows[r].cells[c];
			        rTitle = theCell.innerHTML.replace(/<[^>]+>|&nbsp;/g,'');
			        if(rTitle>""){
			          theCell.title = "Change sort order for " + rTitle;
			          theCell.onmouseover = function(){setCursor(this, "selected")};
			          theCell.onmouseout = function(){setCursor(this, "default")};
			          var sortParams = 15; // bitmapped: numeric|desc|html|freq
			          if(numeric.indexOf("."+colNum+".")>-1) sortParams -= 1;
			          if(desc.indexOf("."+colNum+".")>-1) sortParams -= 2;
			          if(html.indexOf("."+colNum+".")>-1) sortParams -= 4;
			          if(freq.indexOf("."+colNum+".")>-1) sortParams -= 8;
			          theCell.onclick = new Function("sortTable(this,"+(colNum+r)+","+hdrRows+","+sortParams+")");
			        }
			      } else {
			        colNum = colNum+theTab.rows[r].cells[c].colSpan-1;
			      }
			    }
			  }
			}

			function setSort(tabName, colNum, sortDir) {
				var theTab = document.getElementById(tabName);
				theTab.rows[0].sCol = colNum;
				theTab.rows[0].sDir = sortDir;
				if (sortDir)
					theTab.rows[0].cells[colNum].className='sortdown'
				else
					theTab.rows[0].cells[colNum].className='sortup';
			}

			function setCursor(theCell, mode){
			  rTitle = theCell.innerHTML.replace(/<[^>]+>|&nbsp;|\W/g,'');
			  if(mode=="selected"){
			    if(theCell.style.color!=selectedColor)
			      defaultColor = theCell.style.color;
			    theCell.style.color = selectedColor;
			    theCell.style.cursor = "hand";
			    window.status = "Click to sort by '"+rTitle+"'";
			  } else {
			    theCell.style.color = defaultColor;
			    theCell.style.cursor = "";
			    window.status = "";
			  }
			}

			function sortTable(theCell, colNum, hdrRows, sortParams){
			  var typnum = !(sortParams & 1);
			  sDir = !(sortParams & 2);
			  var typhtml = !(sortParams & 4);
			  var typfreq = !(sortParams & 8);
			  var tBody = theCell.parentNode;
			  while(tBody.nodeName!="TBODY"){
			    tBody = tBody.parentNode;
			  }
			  var tabOrd = new Array();
			  if(tBody.rows[0].sCol==colNum) sDir = !tBody.rows[0].sDir;
			  if (tBody.rows[0].sCol>=0)
			    tBody.rows[0].cells[tBody.rows[0].sCol].className='';
			  tBody.rows[0].sCol = colNum;
			  tBody.rows[0].sDir = sDir;
			  if (sDir)
			  	 tBody.rows[0].cells[colNum].className='sortdown'
			  else
			     tBody.rows[0].cells[colNum].className='sortup';
			  for(i=0,r=hdrRows;r<tBody.rows.length;i++,r++){
			    colCont = tBody.rows[r].cells[colNum].innerHTML;
			    if(typhtml) colCont = colCont.replace(/<[^>]+>/g,'');
			    if(typnum) {
			      colCont*=1;
			      if(isNaN(colCont)) colCont = 0;
			    }
			    if(typfreq) {
					switch(colCont.toLowerCase()) {
						case "always":  { colCont=0; break; }
						case "hourly":  { colCont=1; break; }
						case "daily":   { colCont=2; break; }
						case "weekly":  { colCont=3; break; }
						case "monthly": { colCont=4; break; }
						case "yearly":  { colCont=5; break; }
						case "never":   { colCont=6; break; }
					}
				}
			    tabOrd[i] = [r, tBody.rows[r], colCont];
			  }
			  tabOrd.sort(compRows);
			  for(i=0,r=hdrRows;r<tBody.rows.length;i++,r++){
			    tBody.insertBefore(tabOrd[i][1],tBody.rows[r]);
			  }
			  window.status = "";
			}

			function compRows(a, b){
			  if(sDir){
			    if(a[2]>b[2]) return -1;
			    if(a[2]<b[2]) return 1;
			  } else {
			    if(a[2]>b[2]) return 1;
			    if(a[2]<b[2]) return -1;
			  }
			  return 0;
			}
