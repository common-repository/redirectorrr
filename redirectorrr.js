(function() {
	function init() {
		var t = document.getElementById("redirectorrr");
		var d = document.createElement("div");
		d.id = "addRow";
		var b = document.createElement("input");
		b.type = "button";
		b.value = "+";
		t.parentNode.insertBefore(d, t.nextSibling).appendChild(b);
		addDeleteButtons(t);
		b.onclick = addRow;
		//document.createElement("input").type
	}
	
	function addRow() {
		var t = document.getElementById("redirectorrr");
		var r = t.getElementsByTagName("tbody")[0].getElementsByTagName("tr");
		var clone = r[r.length - 1].cloneNode(true);
		var s = clone.getElementsByTagName("select");
		for (var i = 0; i < s.length; i++) {
			s[i].selectedIndex = 0;
		}
		assignDeleteEvent(clone);
		t.getElementsByTagName("tbody")[0].appendChild(clone);
	}
	
	function addDeleteButtons(t) {
		var r = t.getElementsByTagName("tr");
		var td = document.createElement("td");
		var b  = document.createElement("input");
		b.type = "button";
		b.value = "-";
		var it;
		td.appendChild(b);
		for (var i = 0, el; (el = r[i]); i++) {
			if (el.parentNode.tagName.toLowerCase() == "tbody") {
				el.appendChild(td.cloneNode(true));
				assignDeleteEvent(el);
			} else {
				el.appendChild(document.createElement("td"));
			}
		}
	}
	
	function deleteRow() {
		console.log("delereRow");
		if (this.parentNode.parentNode.parentNode.getElementsByTagName("tr").length > 1) {
			this.parentNode.parentNode.parentNode.removeChild(this.parentNode.parentNode);
		} else {
			var s = this.parentNode.parentNode.getElementsByTagName("input");
			for (var i = 0; i < s.length; i++) {
				if (i != 3)
					s[i].value = "";
			}
		}
	}
	
	function assignDeleteEvent(el) {
		console.log(el);
		var it = el.getElementsByTagName("input")[3];
		it.onclick = deleteRow;
	}
	
	
	addLoadEvent(init);
	
})();