function getJSONP(e,t){var n="_"+ +(new Date),r=document.createElement("script"),i=document.getElementsByTagName("head")[0]||document.documentElement;window[n]=function(e){i.removeChild(r);t&&t(e)};r.src=e.replace("callback=?","callback="+n);i.appendChild(r)}var welcomewikiscript=document.getElementById("welcomewikiscript");var urls=welcomewikiscript.getAttribute("data-urls");var sections=welcomewikiscript.getAttribute("data-sections");var sets=welcomewikiscript.getAttribute("data-settings");var source_url=welcomewikiscript.getAttribute("data-source-url");getJSONP(source_url+"/wp-admin/admin-ajax.php?callback=?&url="+urls+"&sections="+sections+"&sets="+sets+"&action=wikilite_embed_action",function(e){console.log("Load success");document.getElementById("welcomewikiscript").innerHTML=e.content})