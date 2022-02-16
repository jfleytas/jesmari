/* IE8 is not supported, but at least it won't look as awful)
/* ========================================================================== */

(function (document) {
	var
	head = document.head = document.getElementsByTagName('head')[0] || document.documentElement,
	elements = 'article aside audio bdi canvas data datalist details figcaption figure footer header hgroup mark meter nav output picture progress section summary time video x'.split(' '),
	elementsLength = elements.length,
	elementsIndex = 0,
	element;

	while (elementsIndex < elementsLength) {
		element = document.createElement(elements[++elementsIndex]);
	}

	element.innerHTML = 'x<style>' +
		'article,aside,details,figcaption,figure,footer,header,hgroup,nav,section{display:block}' +
		'audio[controls],canvas,video{display:inline-block}' +
		'[hidden],audio{display:none}' +
		'mark{background:#FF0;color:#000}' +
	'</style>';

	return head.insertBefore(element.lastChild, head.firstChild);
})(document);

/* Prototyping
/* ========================================================================== */
(function (window, ElementPrototype, ArrayPrototype, polyfill) {
	function NodeList() { [polyfill] }
	NodeList.prototype.length = ArrayPrototype.length;

	ElementPrototype.matchesSelector = ElementPrototype.matchesSelector ||
	ElementPrototype.mozMatchesSelector ||
	ElementPrototype.msMatchesSelector ||
	ElementPrototype.oMatchesSelector ||
	ElementPrototype.webkitMatchesSelector ||
	function matchesSelector(selector) {
		return ArrayPrototype.indexOf.call(this.parentNode.querySelectorAll(selector), this) > -1;
	};

	ElementPrototype.ancestorQuerySelectorAll = ElementPrototype.ancestorQuerySelectorAll ||
	ElementPrototype.mozAncestorQuerySelectorAll ||
	ElementPrototype.msAncestorQuerySelectorAll ||
	ElementPrototype.oAncestorQuerySelectorAll ||
	ElementPrototype.webkitAncestorQuerySelectorAll ||
	function ancestorQuerySelectorAll(selector) {
		for (var cite = this, newNodeList = new NodeList; cite = cite.parentElement;) {
			if (cite.matchesSelector(selector)) ArrayPrototype.push.call(newNodeList, cite);
		}

		return newNodeList;
	};

	ElementPrototype.ancestorQuerySelector = ElementPrototype.ancestorQuerySelector ||
	ElementPrototype.mozAncestorQuerySelector ||
	ElementPrototype.msAncestorQuerySelector ||
	ElementPrototype.oAncestorQuerySelector ||
	ElementPrototype.webkitAncestorQuerySelector ||
	function ancestorQuerySelector(selector) {
		return this.ancestorQuerySelectorAll(selector)[0] || null;
	};
})(this, Element.prototype, Array.prototype);

/* Invoice Functions
/* ========================================================================== */
function generateTableRow() {
	var emptyDetail = document.createElement('tr');     

	//var n = ($('.detalle tr').length-0)+1;
	//document.write (n);

	/*emptyDetail.innerHTML = '<td><a class="cut_detail">-</a><span detallerow></span></td>'+
	'<td><span detallerow><input type="text" id="id_producto" name="id_producto" value="<?php echo $id_producto;?>" onchange="getProducto()" style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();"></span></td>'+
	'<td><span detallerow><input type="text" id="descripcion_producto" name="descripcion_producto" value="<?php echo $descripcion_producto;?>" readonly disabled></span></td>'+
	'<td><span class="detallerow-number"><input type="number" id="cantidad" name="cantidad" value="<?php echo $cantidad;?>"></span></td>'+
    '<td><span detallerow><input type="text" id="id_unidad" name="id_unidad" value="<?php echo $id_unidad;?>" readonly disabled></span></td>'+
	'<td><span class="detallerow-number"><input type="number" id="precio_producto" name="precio_producto" value="<?php echo $precio_producto;?>"></span></td>'+
	'<td><span class="detallerow-number"><input type="number" id="impuesto" name="impuesto" value="<?php echo $impuesto;?>"></span></td>'+
	'<td><span class="detallerow-number"><input type="number" id="descuento" name="descuento" value="<?php echo $descuento;?>"></span></td>'+
	'<td><span class="detallerow-number"><input type="number" id="total_linea" name="total_linea" value="<?php echo $total_linea;?>" readonly></span></td>'
	return emptyDetail;*/
}

function parseFloatHTML(element) {
	return parseFloat(element.innerHTML.replace(/[^\d\,\.\-]+/g, '')) || 0;
}

//Formating the Numbers Columns with Money format and 2 decimals, Millar separator: .
function formatNumber(number) {
	//return number.toFixed(2).replace(/(\d)(?=(\d\d\d)+([^\d]|$))/g, '1,');
	return number.toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,');

}

/* Update Number
/* ========================================================================== */
function updateNumber(e) {
	var
	activeElement = document.activeElement,
	value = parseFloat(activeElement.innerHTML),
	wasNumber = activeElement.innerHTML == formatNumber(parseFloatHTML(activeElement));

	//document.write (activeElement);

	//keyCode == 38 (Up arrow) - e.keyCode == 40 (Down arrow)
	if (!isNaN(value) && (e.keyCode == 38 || e.keyCode == 40 || e.wheelDeltaY)) {
		e.preventDefault();
		//In case that a key Up or Key Down is pressed we add o rest 1 unid. to the quantity field
		value += e.keyCode == 38 ? 1 : e.keyCode == 40 ? -1 : Math.round(e.originalEvent.wheelDelta * 0.025);
		value = Math.max(value, 0);

		activeElement.innerHTML = wasNumber ? formatNumber(value) : value;
	}

	//updateInvoice();
}

/* Update Invoice
/* ========================================================================== */
function updateInvoice() {
	var total_pago = 0;
	var total_pago_linea = 0;
	var total_aplicado = 0;
	var total_aplicado_linea = 0;
	var balance_cells, total_linea, total_general, a, i;
	// update detalle cells
	// ======================
		moneda = document.getElementById('nr_moneda').value;
		if (moneda == 1)
		{
			round = 0;
		}else{
			round = 2;
		}
		detalle_pago = document.getElementById('monto_pago').value;
		detalle_aplicado = document.getElementById('monto_aplicado').value;
		detalle_total_aplicado = document.getElementById('detalle_total_aplicado').value;
		detalle_total_pago = document.getElementById('detalle_total_pago').value;
		$("#total_linea").val(detalle_pago);
		$("#total_linea_aplicado").val(detalle_aplicado);
		//Add Total Pago
		total_pago = Number(detalle_total_pago) + Number(detalle_pago);
		//Add Total Aplicado
		total_aplicado = Number(detalle_total_aplicado) + Number(detalle_aplicado);
	//}

	// update balance cells
	// ====================
	// get balance cells
	balance_cells = document.querySelectorAll('table.balance td:last-child span:last-child');
	//Set Total Aplicado
	$("#total_pago_aplicado").val(total_aplicado);
	//Set Total General
	$("#total_pago").val(total_pago);
	//Set Diferencia
	$("#diferencia").val(total_pago - total_aplicado);

	// update all the cells with type "number" formatting
	// ========================
	/*var prefix = document.querySelector('#prefix').innerHTML;
	for (a = document.querySelectorAll('[detallerow-number]'), i = 0; a[i]; ++i) a[i].innerHTML = prefix;*/

	// Update all the cells with type "number" formatting
	// =======================
	for (a = document.querySelectorAll('span[detallerow-number] + span'), i = 0; a[i]; ++i) 
		if (document.activeElement != a[i]) a[i].innerHTML = formatNumber(parseFloatHTML(a[i]));
}

/* On Content Load
/* ========================================================================== */
function onContentLoad() {
	updateInvoice();

	var
	input = document.querySelector('input'),
	image = document.querySelector('img');

	function onClick(e) {
		var element = e.target.querySelector('[detallerow]'), row;

		element && e.target != document.documentElement && e.target != document.body && element.focus();

		if (e.target.matchesSelector('.add_detail')) {
			//document.querySelector('table.detalle tbody').appendChild(generateTableRow());
		}
		else if (e.target.className == 'cut_detail') {
			row = e.target.ancestorQuerySelector('tr');
			row.parentNode.removeChild(row);
		}

		updateInvoice();
	}

	function onEnterCancel(e) {
		e.preventDefault();

		image.classList.add('hover');
	}

	function onLeaveCancel(e) {
		e.preventDefault();

		image.classList.remove('hover');
	}

	function onFileInput(e) {
		image.classList.remove('hover');

		var
		reader = new FileReader(),
		files = e.dataTransfer ? e.dataTransfer.files : e.target.files,
		i = 0;

		reader.onload = onFileLoad;

		while (files[i]) reader.readAsDataURL(files[i++]);
	}

	function onFileLoad(e) {
		var data = e.target.result;

		image.src = data;
	}

	if (window.addEventListener) {
		document.addEventListener('click', onClick);

		document.addEventListener('mousewheel', updateNumber);
		document.addEventListener('keydown', updateNumber);

		document.addEventListener('keydown', updateInvoice)
		document.addEventListener('keyup', updateInvoice);

		input.addEventListener('focus', onEnterCancel);
		input.addEventListener('mouseover', onEnterCancel);
		input.addEventListener('dragover', onEnterCancel);
		input.addEventListener('dragenter', onEnterCancel);

		input.addEventListener('blur', onLeaveCancel);
		input.addEventListener('dragleave', onLeaveCancel);
		input.addEventListener('mouseout', onLeaveCancel);

		input.addEventListener('drop', onFileInput);
		input.addEventListener('change', onFileInput);
	}
}
window.addEventListener && document.addEventListener('DOMContentLoaded', onContentLoad);