
/**** Flower shop application for market place ***/

// animate input
	function animateInput(eTarget) {

		$(eTarget).focus(function(){
				const idTarget = $(this).attr('id')+"Label";
				$("label#"+idTarget).addClass("focus");
		});

		$(eTarget).each(function(){
			if($(this).val().length != 0) {
				const idTarget = $(this).attr('id')+"Label";
				$("label#"+idTarget).addClass("focusNoAnimate");
			}
		});
	}

// alert
	const FlowerAlert = new function(){
		this.show = function(msg,url=undefined,enterClose=false) {
			if($(".FlowerAlert").length == 0) {
				const bg = document.createElement('div');
				const p = document.createElement('p');
				const a = document.createElement('a');

				const container = document.querySelector("Reza.container-content");

				bg.classList.add("FlowerAlert");
				a.id = 'closeAlert';
				p.id = 'text';
				p.innerHTML = msg;
				a.innerHTML = '<span class="fa fa-remove"></span>';
				bg.append(p);
				bg.append(a);

				container.append(bg);
				$("a#closeAlert").attr('onclick',"FlowerAlert.close('"+url+"')");
				if(enterClose == true)  {
					$("body").attr('onkeypress',"FlowerAlert.close('"+url+"')");
				}

				$(".FlowerAlert").fadeIn();

			} else {
				$(".FlowerAlert").fadeIn();
				$(".FlowerAlert p#text").html(msg);

				$("a#closeAlert").attr('onclick',"FlowerAlert.close('"+url+"')");
				if(enterClose == true)  {
					$("body").attr('onkeypress',"FlowerAlert.close('"+url+"')");
				}
			}
		}

		this.close = function(url,event){
			$(".FlowerAlert").fadeOut();
			if(url != 'undefined') {
				window.location = url;
			}
		}
	}

// get
	function GET(param) {

		if(typeof param != "undefined") {
			const arrHasilGet = []
			let hash = '';
			const arrDataGet = window.location.href.slice(window.location.href.indexOf('?')+1).split('&');
			for(let i = 0; i < arrDataGet.length; i++) {
				hash = arrDataGet[i].split('=');
				arrHasilGet.push(hash[0]);
				arrHasilGet[hash[0]] = hash[1];
			}

			return arrHasilGet[param];
		} else {
			return undefined;
		}
	}

// header mobile
	$(function(){
		$("#showMobileHeader").click(function(){
			$(".mobileHeader").fadeIn();
		})
		$("#closeMobileHeader").click(function(){
			$(".mobileHeader").fadeOut();
		})
	})

// dropdown
	const conDropdown = document.querySelector(".menuHeader");
	if(conDropdown != null) {
		conDropdown.addEventListener("click",function(e){
			let target = e.target;
			if(target.classList.contains('btnDropdown') === false) target = e.target.parentElement;
			if(target.classList.contains('btnDropdown') === true) {
				const idDropdown = target.getAttribute('dropdown');
				const dropdown = conDropdown.querySelector("#"+idDropdown);
				// hide all dropdown
				conDropdown.querySelectorAll(".menuDropdown").forEach(function(v){
					if(v.getAttribute('id') != idDropdown) {
						v.classList.remove('muncul');
					}
				});

				if(dropdown != null) {
					dropdown.classList.toggle('muncul');
				}
			}
		});
	}

// home page
	function tampilAbout() {
		$("div.about h1").addClass("muncul");
		$("div.about #left").addClass("muncul");
		$("div.about #right").addClass("muncul");
		// mencegah agar chrevDown#toProduk tidak ditampilakn lagi pada saat scroll dan produk sudah di tampilkan dan sebaliknya
					
			setTimeout(function(){
				if($(".produk .divProduk .tampilProduk.muncul").length == 0) {
					$("div.about .chrevDown").addClass("muncul");
				} else {
					$("div.about .chrevDown").removeClass("muncul");
				}
			},1000)
	}

	if($('html, body')[1].clientWidth == 768) {
		tampilAbout();
	}

	$(window).scroll(function(){
		if(!document.querySelector(".menuHeader").classList.contains("noAnimate")) {
			if($('html, body').scrollTop() > 10) {
				$(".menuHeader").removeClass("remove");
				$(".menuHeader").addClass("white");
			} else {
				$(".menuHeader").addClass("remove");
			}
		}

		// about
		if($("div.about").length != 0 && $('html, body').scrollTop() >= $("div.about").offset().top-350) {
			tampilAbout();
		}

		// produk
		if($("div.about").length != 0 && $('html, body').scrollTop() >= $("div#produk").offset().top-350) {
			$(".divProduk .tampilProduk").addClass("muncul");
			$("div.about .chrevDown").removeClass("muncul");
			$(".produk .judul").addClass("muncul")
		}
	})

	$(".menuHeader a.to").click(function(eP){
		const e = $(this);
		scroll(e,eP);
	})

	$("#toAbout").click(function(eP){
		const e = $(this);
		scroll(e,eP);
	})

	$("#toProduk").click(function(eP){
		const e = $(this);
		scroll(e,eP,90);
	})

	function scroll(e,eP,minus=70) {
		const element = $(e).attr('href');
		const reg = /https?/i;
		
		if(!reg.test(element)) {
			const elementTarget = $(element);
			eP.preventDefault();

			$("html, body").animate({
				scrollTop: elementTarget.offset().top-minus
			}, 1200, "easeInOutQuint");
		}
	}

// detail produk
	$("a#infoProduk").click(function(){
		$(".InformasiProduk").addClass("muncul");
		$(".menuInUlProduk li a").removeClass("active");
		$("li a#infoProduk").addClass("active");
		$(".ulasanProduk").removeClass("muncul");
	})
	$("a#ulasanProduk").click(function(){
		$(".InformasiProduk").removeClass("muncul");
		$(".menuInUlProduk li a").removeClass("active");
		$("li a#ulasanProduk").addClass("active");
		$(".ulasanProduk").addClass("muncul");
	});

// input dropdown
	const conInputDropdown = document.querySelector("div.conInputDropdown");
	if(conInputDropdown != null) {
		conInputDropdown.addEventListener('click', function(e){
			let target = e.target;
			if(target.classList.contains("btnInputDropdown") == false) {
				target = e.target.parenElement;
			}

			if(target != undefined && target.classList.contains("btnInputDropdown") == true) {
				const idInputDropdown = target.getAttribute('target');
				const inputDropdown = document.querySelector("#"+idInputDropdown);
				inputDropdown.classList.toggle("muncul");
			}
		});
	}

// thumbnail image
	const conThumbnails = document.querySelector(".conThumbnails");
	if(conThumbnails != null) {
		conThumbnails.addEventListener('click',function(e){
			if(e.target.classList.contains('thumbnail')) {

				const thumbnail = e.target;
				const bgThumbnails = thumbnail.parentElement;
				bgThumbnails.classList.toggle('bgThumbnails');
				bgThumbnails.classList.toggle('zoom');
				thumbnail.classList.toggle('zoom');
			}
		});
	}

// input number
	let element = '<div class="quantity-nav">';
	element += '<div class="quantity-button quantity-up">+</div>';
	element += '<div class="quantity-button quantity-down">-</div>';
	element += '</div>';
	$(element).insertAfter('.quantity input');

	$(".quantity").each(function(){
		const spinner = this;
		const input = spinner.querySelector(".quantity input");
		const btnPlus = spinner.querySelector(".quantity-up");
		const btnMinus = spinner.querySelector(".quantity-down");

		btnPlus.addEventListener('click', function(){
			const max = parseInt(input.getAttribute("max"));
			let oldValue = input.value;
			if(oldValue.length == 0) { oldValue = 0; }
			if(oldValue.length != 0) { parseInt(oldValue); }

			// tambah oldValue
			oldValue++;

			if(max != null && max != 0 && oldValue <= max) {
				input.value = oldValue;
			} else {
				input.value = "";
			}
		});

		btnMinus.addEventListener('click',function(){
			const max = parseInt(input.getAttribute("max"));
			let oldValue = input.value;
			if(oldValue.length == 0) { oldValue = 0; }
			if(oldValue.length != 0) { parseInt(oldValue); }

			// kurang old value
			oldValue--;

			if(oldValue > 0) {
				input.value = oldValue;
			} else {
				input.value = "";
			}
		});

		input.addEventListener('input',function(){
			const max = parseInt(input.getAttribute("max"));
			let value = input.value;
			if(value <= 0 || max == 0) {
				input.value = "";
			} else if(value > max) {
				input.value = max;
			}
		});
	});

// table caret down
	const table = document.querySelector("table");
	if(table != null) {
		table.addEventListener('click', function(e){
			let target = e.target;
			if(!target.classList.contains("caret")) target = e.target.parentElement;

			if(target.classList.contains("caret")) {
				const caretDown = table.querySelector("tr#"+target.getAttribute("caret"));
				caretDown.classList.toggle("muncul");
				target.classList.toggle("active");
			}
		});
	}

// input search
	const searchInput = document.querySelector("input#keyword");
	if(searchInput != null) {
		const btnsearch = document.querySelector('a#search');
		searchInput.addEventListener("input", function(e){
			const val = e.target.value.replace(/\s/g,"");
			if(val.length != 0) {
				if(!btnsearch.classList.contains("muncul")) {
					btnsearch.classList.add("muncul");
				}
			} else {
				if(btnsearch.classList.contains("muncul")) {
					btnsearch.classList.remove("muncul");	
				}
			}
		});
	}

// format harga
	function format_harga(number, decimal, dec_point, thousands_sep) {

		// check dec_point and thousands_sep
		if((dec_point == undefined && thousands_sep != undefined)||(dec_point != undefined && thousands_sep == undefined)) {
			// exit from function
			return '';
		}

		let regexNumberValid = /^[0-9]+\.*[0-9]*$/;
		if(regexNumberValid.test(number)) {

			dec_point = typeof dec_point != "string"?".":dec_point;
			thousands_sep = typeof thousands_sep != "string"?",":thousands_sep;
			decimal = typeof decimal != "number"?0:decimal;

			let regexFindPoint = /\./;
			let hargaReal;
			let hargaDecimal;
			// parse string input to float supaya bisa di bulatkan
			number = parseFloat(number).toFixed(decimal);

			if(regexFindPoint.test(number)) {
				number = number.split(".");
				hargaReal = number[0].split("");
				hargaDecimal = dec_point+number[1];

			} else {
				hargaReal = number.split("");
			}

			let hitungPosisiTitik = 1;
			let posisiTitik = 4;
			// add thousands sep
			for(let i=hargaReal.length-1; i>=0; i--) {
				if(hitungPosisiTitik == posisiTitik) {
					hargaReal[i] = hargaReal[i]+thousands_sep;
					posisiTitik += 3;
				}
				hargaReal[i] = hargaReal[i];
				hitungPosisiTitik++;
			}

			return hargaReal.join("")+hargaDecimal;
		} else {
			return '';
		}
	}