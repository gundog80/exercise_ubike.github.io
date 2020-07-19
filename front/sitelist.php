<!-- <button id="trybutton">try</button> -->
<ul id=right-siderbar name=right-siderbar 
  class="col-auto nav flex-sm-column align-items-end">
	<li class="nav-item">
		<button class="siteNameBtn btn btn-sm btn-pill btn-orange" type="submit"
		 data-site="">三重區</button>
	</li>
</ul>
<div class=col-sm>
	<div>
		<span id=actionGuide>請選擇行政區 或 </span>
		<button class=" btn-sm btn-orange btn-pill" id=getPosition>取得附近站點</button>
		<br>
		<span id=positionSorry class=d-none>因網頁安全認證問題，無法使用navigator API，定位會有較大誤差，此功能僅為示範 或
		<button class=" btn-sm btn-orange btn-pill" id=getTestPosition>模擬隨機地點</button></span>
		<!-- <button class=" btn-sm btn-orange btn-pill" id=getPosition>取得附近站點</button> -->
	</div>
	<div id=mapArea class="col-10">
		<?php
		echo file_get_contents("areaMap.svg")
		?>
	</div>
	<iframe name=mpaIframe id=mpaIframe frameborder="0" style="border:0;" allowfullscreen="" aria-hidden="false" tabindex="0"
	  class="d-none col-10 vh-50" src="" ></iframe>
	<table id=siteTable class="d-none table container table-striped table-hover">
		<thead class="bg-orange">
			<tr class=""><th class="d-block">
				<div class="nav" >
					<div class="col-12 col-md-6">
						<ul class=nav>
							<li class="col-auto border-2 border-primary">站點名稱</li>
							<li class="col border-2 border-primary">車數/空位</li>
						</ul>
					</div>
					<div class="col-12 col-md-6">
						<ul class=nav>
							<li class="col">地址</li>
							<li class="col-auto">查看地圖</li>
						</ul>
					</div>
				</div>
			</th></tr>
		</thead>
		<tbody class="">
		</tbody>
	</table>
</div>

<script src="./lib/jquery-3.4.1.min.js"></script>
<script>

  $(()=>{
		showAreaList();
		let nowSite="三重區";
		let siteData=getAJAXData()
		let positon={lat:121,lng:25}
		setTimeout(()=>{
			// showTable(siteData,nowSite)
			$("#right-siderbar" ).on("click","button",function(){
				nowSite=$(this).attr("data-site")
				showAreaTable(siteData,nowSite)
				
			})
			monitorPath(siteData)
			monitorGetPosition(siteData)
			},500)
	})
	//監聽事件
	monitorPath=function(a){
		//svg地圖監聽
		$('path').on("click",(event)=>{
			let pathNO=$(event.target).attr('id')
			console.log(pathNO)
			nowSite=pathArea=$(event.target).attr('data-area')
			console.log(nowSite)
			showAreaTable(a,nowSite)
		})
	}

	monitorGetPosition=function(allSite){
		//附近站點確認
		function chkNearSite(position,allSite){
			return Promise.all([position,allSite]).
			then((values)=>{
				let position=values[0]
				// console.log("chkNS")
				// console.log(position.lng)
				// alert(position.lng)
				let allSite=values[1]
				let nearSiteList=new Array
				// site=allSite[0]
				showPositionMap(position)
				// alert("position2.lat=" + position.lat + "<br>" + "position2.lng=" + position.lng)
				allSite.forEach(site=>{
					let distance=(Math.sqrt(Math.pow((site.lat-position.lat),2)+Math.pow((site.lng-position.lng)*Math.cos(position.lat),2)))
						// alert("distance" + distance)
					if(distance*111<1){
						// alert("distanceagain=" + distance)
						site['distance']=distance*111
						nearSiteList.push(site)
						// alert("nearSiteList=" + nearSiteList)
						// alert("nearSiteList.length=" + nearSiteList.length)
						// alert("nearSiteList[0]=" + nearSiteList[0])
					}
				})

				// alert(nearSiteList)
				return nearSiteList
			})
		}
			// 顯示地圖
	
		function showPositionMap(position){
			let gsrc=`https://www.google.com/maps?q=${position.lat},${position.lng}&output=embed`
			// console.log(gsrc)
			$("#mpaIframe").attr("src",gsrc)
			$("#mpaIframe").removeClass("d-none")

		}


		$('#getPosition').on("click",(event)=>{
			// let position
			let position=(getPosition())
			// getPosition().then(value=>{console.log(value)})
			let nearSite=chkNearSite(position,allSite).
			then(value=>{
				console.log(value)
				writeTable(value)
			}).
			then((value)=>{
				$("#mapArea").addClass("d-none")
				$("#actionGuide").addClass("d-none")
				$("#siteTable").removeClass("d-none")
				$("#getPosition").text("重新取得附近站點")
				$("#positionSorry").removeClass("d-none")
			})
		})

		$('#getTestPosition').on("click",(event)=>{
			// 寄放，getTestPosition監聽
			let position=(getTestPosition())
			// getPosition().then(value=>{console.log(value)})
			let nearSite=chkNearSite(position,allSite).
			then(value=>{
				console.log(value)
				writeTable(value)
			}).
			then((value)=>{
				$("#mapArea").addClass("d-none")
				$("#actionGuide").addClass("d-none")
				$("#siteTable").removeClass("d-none")
				$("#getPosition").text("重新取得附近站點")
			})
		})
	}


	// showAreaList 相關
	function getInternalData(data) {
		return new Promise(function (resolve, reject) {
			console.log("hi2")
			$.post("./api/data.php",{data},function(resData){
				resolve(JSON.parse(resData))
			});
		});
	};
	function showAreaList(){
		getInternalData("areaList").
		then((value)=>{
			$("#right-siderbar").html("")
			value.forEach(area => {
				$("#right-siderbar").append(
				  `<li class="nav-item"><button class="siteNameBtn btn btn-pill btn-orange" type="submit"data-site="${area}">${area}</button></li>`)
				});
			return(value)
		})
	}

	// 資料取得
	getAJAXData=function(){
		// 取得站內資料
		let url=`https://quality.data.gov.tw/dq_download_json.php?nid=123026&md5_url=4d8de527a0bcd8a7b1aeae91120f021d`
		let AJAXdata=getJSON(url).
		then((value) =>{
			value=value.substring(0,value.indexOf(']')+1)
			return JSON.parse(value)
			})
		return AJAXdata
	}

	function getJSON(url) {
		// 取得新北市ubike資料
	    return new Promise(function (resolve, reject) {
	        $.post("./api/getJSON.php",{url},function(data){
	            resolve(data)
	        });
	    });
	};

		// 地點取得

		function getTestPosition(){
			// 模擬geolocation api 測試用
			return new Promise(function (resolve, reject) {
				let lng = 121.4 + Math.random()*0.05;
				let lat = 25.0 + Math.random()*0.2;
				let position={}
				position.location={lat,lng}
				position.accuracy=123456
				console.log(position)
				resolve(position.location)
	    	});
		}
		
		// monitorGetPosition=function(){
		// 	navigator法 需https
		// 	$('#getPosition').on("click",(event)=>{
		// 		navigator.geolocation.getCurrentPosition((position) => {
		// 	    console.log(position.coords);
		// 	    let lat = position.coords.latitude;
		// 	    let lng = position.coords.longitude;
		// 	    console.log(lat);
		// 	    console.log(lng);
		// 		})
		// 	})
		// }

		function getPosition(){
			// geolocation api法 需開通付費
			var geolocation = 'https://www.googleapis.com/geolocation/v1/geolocate?key=AIzaSyAx8urlErdmGC74djnoImCUmOsyWb_aIco';
			return new Promise(function (resolve, reject) {
				xhr = new XMLHttpRequest();
				xhr.open('POST', geolocation);
				let waitO=function(){
					setTimeout(()=>{
						if(xhr.readyState==4){
							console.log("xhr")
							// alert("xhr=" + xhr.responseText
							// + "positon1.lat=" + JSON.parse(xhr.responseText).location.lat
							// + "positon1.lng=" + JSON.parse(xhr.responseText).location.lng)
							// console.log(xhr.responseText.location.lat)
							// alert(xhr.responseText.location.lat)
							// console.log(JSON.parse(xhr.responseText))
							// alert(JSON.parse(xhr.responseText).location.lat)
							alert("accuracy=" + JSON.parse(xhr.responseText).accuracy)
							resolve(JSON.parse(xhr.responseText).location)
						}else{
							console.log("wait")
							waitO()
						}
					},100)
				}
				waitO()
				xhr.send();
			});
		}


	// showTable() 相關
	showAreaTable=function(siteData,chk){
		let areaData=new Array
		siteData.
		then((value)=>{
			value.forEach(site=>{
				if(site.sarea==chk){
					areaData.push(site)
				}
			})
			return areaData
		}).
		then((value)=>{
			writeTable(value)
			return value
		}).
		then((value)=>{
			$("#mapArea").addClass("d-none")
			$("#actionGuide").addClass("d-none")
			$("#siteTable").removeClass("d-none")
		})
	}

	writeTable=function(value){
		$("#siteTable>tbody").html("")
		console.log("進入wirteTable")
		console.log(value)
		value.forEach(site=>{
			$("#siteTable>tbody").append(`<tr class=""><td class="row d-block"><div class="nav"></div></td></tr>`)
			$("#siteTable>tbody div").last().append(`<div class="col-12 col-md-6"><ul class=nav></ul></div>` )
			$("#siteTable>tbody ul").last().append(`<li style="max-width:7em;"class="col-auto border-2 border-primary">${site.sna}</li>`)
			$("#siteTable>tbody ul").last().append(`<li class="col border-2 border-primary">${site.sbi}/${site.bemp}</li>`)
			$("#siteTable>tbody div").last().after(`<div class="col-12 col-md-6"><ul class=nav></ul></div>` )
			$("#siteTable>tbody ul").last().append(`<li class="col">${site.ar}</li>`)
			$("#siteTable>tbody ul").last().append(`<li class="col-auto"><button class="font-weight-bold text-success btn btn-outline-dark gmap" target="mpaIframe"  data-iframeSrc="https://www.google.com/maps?q=${site.lat},${site.lng}&output=embed">點我</button></li>`)
		})
		return value
	}

// google地圖操作相關
$("table").on("click",".gmap",function(){
	let gsrc=$(this).attr("data-iframeSrc")
	console.log(gsrc)
	$("#mpaIframe").attr("src",gsrc)
	$("#mpaIframe").removeClass("d-none")
})



//svg操作相關
	//svg滑入滑出
	$('path').mouseenter((event)=>{
		$(event.target).attr('transform','translate(-5,-5)')
		$(event.target).insertAfter($("svg path").last());
	} )
	$('path').mouseleave((event)=>{
		$(event.target).attr('transform','translate(0, 0)')
	})
	//svg滑入滑出end




	



	
</script>
