<?php ?>
<script type = "text/javascript" src = "jquery.js"></script>
<script type = "text/javascript" src = "particle.js"></script>
<script type="text/javascript">

	var loading;
	var canvas;
	var g;


	var p = new Array();
	var numParticles = <?php echo count($article["title"]); ?>;
	var attractionForce = new Array();
	
	var title = new Array();
	var abstract = new Array();
	var section = new Array();
	var url = new Array();
	
	var sectionLookUp = new Array();
	sectionLookUp[0] = "World";
	sectionLookUp[1] = "U.S.";
	sectionLookUp[2] = "Politics";
	sectionLookUp[3] = "N.Y. / Region";
	sectionLookUp[4] = "Business";
	sectionLookUp[5] = "Dealbook";
	sectionLookUp[6] = "Technology";
	sectionLookUp[7] = "Sports";
	sectionLookUp[8] = "Science";
	sectionLookUp[9] = "Health";
	sectionLookUp[10] = "Opinion";  
	sectionLookUp[11] = "Arts";
	sectionLookUp[12] = "Books";
	sectionLookUp[13] = "Movies";
	sectionLookUp[14] = "Music";
	sectionLookUp[15] = "Television";
	sectionLookUp[16] = "Theater";
	sectionLookUp[17] = "Style";
	sectionLookUp[18] = "Automobiles";
	sectionLookUp[19] = "Travel";
	
<?php
	for($l=0;$l<count($article["title"]);$l++) {
		echo "title[" . $l . "] = \"" . $article["title"][$l] . "\"; \n";
	}
	for($l=0;$l<count($article["abstract"]);$l++) {
		echo "abstract[" . $l . "] = \"" . $article["abstract"][$l] . "\"; \n";
	}
	for($l=0;$l<count($article["section"]);$l++) {
		echo "section[" . $l . "] = \"" . $article["section"][$l] . "\"; \n";
	}
	for($l=0;$l<count($article["url"]);$l++) {
		echo "url[" . $l . "] = \"" . $article["url"][$l] . "\"; \n";
	}
?>


//--------------------------------------------------
	window.onload = function() {			
		
		loading = document.createElement('img');
		loading.setAttribute('src', 'loading.gif');
		loading.setAttribute('alt', '');
		document.body.appendChild(loading);
		
		canvas = document.createElement('canvas');
		canvas.setAttribute('width', window.innerWidth);
		canvas.setAttribute('height', window.innerHeight);
		
		g = canvas.getContext('2d');
		
		for(i = 0; i < numParticles; i++){
			
			var x = Math.random() * canvas.width;
			var y = Math.random() * canvas.height;
			var size = map(numParticles - i, 0, 160, 24, 72);
			
			p[i] = new Particle( x, y, 0.0, 0.0, size, i + 1, title[i], section[i], g);
			attractionForce[i] = map(p[i].num, 1, 160, 0.0009, 0.003);

		}
		
		setup();
		
	};

	
//--------------------------------------------------		
	function setup() {
		
		canvas.addEventListener('mousemove', mousemoved, false);
		canvas.addEventListener('mousedown', mousePressed, false);
		canvas.addEventListener('mouseup', mouseReleased, false);
		
		document.body.removeChild(loading);
		document.body.appendChild(canvas);

		setInterval(update, 1000/30);
		setInterval(draw, 1000/30);

	};


	var avgX = new Array();
	var avgY = new Array();
	var avgDenom = new Array();

//--------------------------------------------------		
	function update() {
		numParticles = p.length;
		
		
		for(i = 0; i < 20; i++){
			avgX[i] = 0;
			avgY[i] = 0;
			avgDenom[i] = 0;
		}
		
		for (i = 0; i < numParticles; i++) {
			for(j = 0; j < 20; j++){
				if(p[i].section.toLowerCase() == sectionLookUp[j].toLowerCase()){

					avgX[j] += p[i].posX;
					avgY[j] += p[i].posY;
				
					avgDenom[j]++;
				}
			}
		}

		for(i = 0; i < 20; i++){			
			avgX[i] = avgX[i] / avgDenom[i];
			avgY[i] = avgY[i] / avgDenom[i];
		}
		
		
		
		for (i = 0; i < numParticles; i++) {
			p[i].resetForce();
		}
		
		for (i = 0; i < numParticles; i++) {
			for (j = 0; j < numParticles; j++){
				
				p[i].addRepulsionForce(p[j].posX, p[j].posY, (p[i].width/2) + (p[j].width/2) - 2, 1.0);
				p[i].addRepulsionForce(p[j].posX, p[j].posY, (p[i].width/2) + (p[j].width/2), 1.0);
				p[i].addRepulsionForce(p[j].posX, p[j].posY, (p[i].width/2) + (p[j].width/2) + 2, 1.0);
			}

			
			for (j = 0; j < 20; j++){
				if(p[i].section == sectionLookUp[j]){
					
					p[i].addAttractionForce(avgX[j], avgY[j], canvas.width*1.5, attractionForce[i]);
	
				}
			}
		
		}

		for (i = 0; i < numParticles; i++) {
			
			p[i].dampingForce();
			p[i].update();
			p[i].mouseOver(mX, mY, mP);
			p[i].bounceOffWalls();
			
		}
					
	};

	var bOver = false;
	var overIndex = -1;
	var counter = 0;
//--------------------------------------------------		
	function draw() {
		g.clearRect(0,0,canvas.width, canvas.height);
	
		g.fillStyle = "rgba( 0, 0, 0, 0.15)";
		g.textAlign = "center";
		g.font = "60px times-bold";
		g.fillText("New York Times Most Read Articles", canvas.width/2, 50);
		
		g.font = "36px times";
		g.fillText("please explore the current 160 most read articles", canvas.width/2, 80);
		
		for (i = 0; i < numParticles; i++) {

			p[i].draw();
			p[i].drawText();
			
			if(p[i].over == true){
				overIndex = i;
			}
			if(overIndex == i && bOver == false){
				bOver = true;

				divIn(i);

				counter++;

			}
			
			if(p[i].over == false && overIndex == i){
				bOver = false;
				divOut(overIndex);
				overIndex = -1;
			}
			
		}
		
	};
	
	var div;
	var side;
//--------------------------------------------------		
	function divIn(index) {

			if(div != null){
				document.body.removeChild(div);
			}

			div = document.createElement('div');
			div.setAttribute('id', index);
			div.setAttribute('class', 'articleDetails');
			
			div.style.width = canvas.width / 2 - 30 + "px";
			div.style.top = p[i].posY - 50 + "px";
			
			if(p[i].posX > canvas.width / 2){
				side = 'l';
				div.style.left = "-1000px";
			} else {
				side = 'r';
				div.style.left = (canvas.width + 200) + "px";
			}
			
			div.innerHTML = "<div class=\"sect\" style=\"background:" + getColor(section[i]) + "\">" + section[i] + "</div><div class=\"details\"><b>#" + index + " - " + title[i] + "</b><p>" + abstract[i] + "</p>" + "<a href=\"" + url[i] + "\">" + url[i] + "</a></div>";
			document.body.appendChild(div);
			
			if(side == 'l'){
				$(".articleDetails#" + index).animate({left: 10},"slow");
			} else {
				$(".articleDetails#" + index).animate({left: (canvas.width / 2 + 20) + "px"},"slow");
			}

	};
	
//--------------------------------------------------		
	function divOut(index) {
		if(side == 'l'){
			$(".articleDetails#" + index).animate({left: -600 + "px", display: "hidden"},"slow");
		} else {
			$(".articleDetails#" + index).animate({left: (canvas.width + 600) + "px", display: "hidden"},"slow");
		}
	};

	
	var mX;
	var mY;
	var mP;

//--------------------------------------------------
	function mousemoved(e) {
		mX = e.offsetX;
		mY = e.offsetY;
	};

//--------------------------------------------------
	function mousePressed() {
		mP = true;
	};

//--------------------------------------------------
	function mouseReleased() {
		mP = false;
	};

//--------------------------------------------------	
	function map(position, min1, max1, min2, max2) {
	    return min2 + (max2 - min2) * ((position - min1) / (max1 - min1));
	}

//--------------------------------------------------
	function getColor(section){

		if(section == "World"){
			
			return "rgba( 220, 20, 60, 0.65)";
			
		} else if(section == "U.S."){
			
			return "rgba( 0, 0, 238, 0.65)";
			
		} else if(section == "Politics"){
			
			return "rgba( 255, 153, 18, 0.65)";
			
		} else if(section == "N.Y. / Region"){
			
			return "rgba(61, 89, 171, 0.65)";
			
		} else if(section == "Business"){
			
			return "rgba( 255, 215, 0, 0.65)";
			
		} else if(section == "Dealbook"){
			
			return "rgba( 127, 255, 0, 0.65)";
			
		} else if(section == "Technology"){
			
			return "rgba( 128, 128, 0, 0.65)";
			
		} else if(section == "Sports"){
			
			return "rgba(128, 0, 0, 0.65)";
			
		} else if(section == "Science"){
			
			return "rgba( 255, 127, 80, 0.65)";
			
		} else if(section == "Health"){
			
			return "rgba( 55, 20, 147, 0.65)";
			
		} else if(section == "Opinion"){
			
			return "rgba( 139, 69, 19, 0.65)";
			
		} else if(section == "Arts"){
			
			return "rgba( 105, 139, 34, 0.65)";
			
		} else if(section == "Books"){
			
			return "rgba( 152, 251, 152, 0.65)";
			
		} else if(section == "Movies"){
			
			return "rgba( 255, 69, 0, 0.65)";
			
		} else if(section == "Music"){
			
			return "rgba( 64, 224, 208, 0.65)";
			
		} else if(section == "Television"){
			
			return "rgba( 238, 238, 0, 0.65)";

		} else if(section == "Theater"){

			return "rgba( 139, 115, 85, 0.65)";
				
		} else if(section == "Style"){
			
			return "rgba( 148, 0, 211, 0.65)";
			
		} else if(section == "Automobiles"){
			
			return "rgba( 0, 139, 139, 0.65)";
			
		} else if(section == "Travel"){
			
			return "rgba( 47, 79, 79, 0.65)";
			
		} else {
			
			return "rgba( 127, 127, 127, 0.65)";
				
		}
		
	}
	
</script>