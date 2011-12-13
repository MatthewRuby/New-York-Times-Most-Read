function Particle(px, py, vx, vy, size, num, title, section, ctx){

		this.width = size;
		this.num = num;
		
		this.title = title;
		this.section = section;

		this.posX = px;
		this.posY = py;

		this.velX = vx;
		this.velY = vy;
		
		this.frcX = 0;	
		this.frcY = 0;
		
		this.damping = 0.2;
		
		this.ctx = ctx;
		
		this.prevPosX = px;
		this.prevPosY = py;

		this.angle = 0;
		this.prevAngle = 0;
		this.over = false;
		
}
	
Particle.prototype = {
	
	resetForce: function(){
		this.frcX = 0;
		this.frcY = 0;
	},
	
	addForce: function(x, y){				
		this.frcX = this.frcX + x;	
		this.frcY = this.frcY + y;
	},
	
	dampingForce: function(){
		this.frcX = this.frcX - this.velX * this.damping;
		this.frcY = this.frcY - this.velY * this.damping;
	},
	
	update: function(){
		this.velX = this.velX + this.frcX;
		this.velY = this.velY + this.frcY;
		
		if(this.velX > 4.0){
			this.velX = 4.0;
		} else if(this.velX < -4.0){
			this.velX = -4.0;
		}
		
		if(this.velY > 4.0){
			this.velY = 4.0;
		} else if(this.velY < -4.0){
			this.velY = -4.0;
		}
		
		this.posX = this.posX + this.velX;
		this.posY = this.posY + this.velY;
		
		var dx = this.posX - this.prevPosX;		
		var dy = this.posY - this.prevPosY;
		
		this.angle = 0.01 * Math.atan2(dy,dx) + (1 - 0.01) * this.angle;
		
		this.prevPosX = this.posX;
		this.prevPosY = this.posY;

	},
	
	draw: function(){

		var x = this.posX;
		var y = this.posY;
		
		var color = getColor(this.section);

		this.ctx.fillStyle = color;
		this.ctx.save();
			
	    this.ctx.translate(x, y);
	
		this.ctx.rotate(this.angle);
		
		roundedRect( 0 - (this.width/2), 0 - (this.width/2), this.width, this.width, this.width/2, this.ctx);
		
		this.ctx.restore();
		
		this.ctx.fill();

	},
	
	drawText: function(){
		
		var fontSize = map(this.width, 16, 60, 9, 24);
		var baseline = this.posY + fontSize/2;
		
		this.ctx.fillStyle = "rgba( 255, 255, 255, 1.0)";
		this.ctx.save();
	    this.ctx.translate(this.posX, this.posY);

		this.ctx.rotate(this.angle);

		this.ctx.textAlign = "center";
		this.ctx.font = fontSize + "px courier";

		this.ctx.fillText(this.num, 0, fontSize/4);
	    this.ctx.restore();
	},
	
	hover: function(){
		
		var fontSize = 18;
		var baseline = this.posY + fontSize/2;
		
		this.ctx.fillStyle = "rgba( 0, 0, 0, 0.8)";
		this.ctx.save();
	    this.ctx.translate(this.posX, this.posY);

		this.ctx.rotate(this.angle * 0.1);

		this.ctx.textAlign = "center";
		this.ctx.font = "16px courier";
		this.ctx.fillText(this.title, 0, fontSize/3);
	    this.ctx.restore();
	},
	
	bounceOffWalls: function(){
		this.bDidICollide = false;

		this.minX = (this.width/2);
		this.minY = (this.width/2);
		this.maxX = canvas.width - (this.width/2);
		this.maxY = canvas.height - (this.width/2);

		if (this.posX > this.maxX){
			this.posX = this.maxX; // move to the edge, (important!)
			this.velX *= -1;
			this.bDidICollide = true;
		} else if (this.posX < this.minX){
			this.posX = this.minX; // move to the edge, (important!)
			this.velX *= -1;
			this.bDidICollide = true;
		}

		if (this.posY > this.maxY){
			this.posY = this.maxY; // move to the edge, (important!)
			this.velY *= -1;
			this.bDidICollide = true;
		} else if (this.posY < this.minY){
			this.posY = this.minY; // move to the edge, (important!)
			this.velY *= -1;
			this.bDidICollide = true;
		}
	},

	collide: function(other){

	  	var diffX = this.posX - other.posX;
	  	var diffY = this.posY - other.posY;

	  	var dist = Math.sqrt(diffX*diffX + diffY*diffY);
		var minDist = (other.width/2) + (this.width/2) + 2;

	  	if(dist < minDist){

			var angle = Math.atan2(diffX, diffY);
			
	        var targetX = this.posX + Math.cos(angle) * minDist;
	        var targetY =this.posY + Math.sin(angle) * minDist;
	
	        var ax = (targetX - other.posX);
	        var ay = (targetY - other.posY);

	        this.velX -= ax;
	        this.velY -= ay;
	        other.velX += ax;
	        other.velY += ay;
			
		}
		  
	},

	addRepulsionForce: function(px, py, radius, strength){

	  	var posOfForceX = px;
	  	var posOfForceY = py;

	  	var diffX = this.posX - px;
	  	var diffY = this.posY - py;

	  	var length = Math.sqrt(diffX*diffX + diffY*diffY);

	  	if(length < radius){
		
	  		var pct = 1 - (length / radius);
	
	  		diffX = diffX * 0.1;
	  		diffY = diffY * 0.1;
	
	  		this.frcX = this.frcX + diffX * pct * strength;
	  		this.frcY = this.frcX + diffY * pct * strength;
		}
		
	},

	addAttractionForce: function(px, py, radius, scale){
		var posOfForceX = px;
		var posOfForceY = py;

	  	var diffX = this.posX - posOfForceX;
	  	var diffY = this.posY - posOfForceY;

	  	var length = Math.sqrt(diffX*diffX + diffY*diffY);

	  	if(length < radius){
		
	  		var pct = 1 - (length / radius);
	
	  		diffX = diffX * 0.1;
	  		diffY = diffY * 0.1;
	
	  		this.frcX = this.frcX - diffX * pct * scale;
	  		this.frcY = this.frcY - diffY * pct * scale;
	  	}
	},



	mouseOver: function(mouseX, mouseY, down) {

		var mousePressed = down;

		if (mouseX * mouseX < ( this.posX + (this.width / 2)) * (this.posX + (this.width / 2))
			&& mouseX * mouseX > (this.posX - (this.width / 2)) * (this.posX - (this.width / 2))
			&& mouseY * mouseY < (this.posY + (this.width / 2)) * (this.posY + (this.width / 2))
			&& mouseY * mouseY > (this.posY - (this.width / 2)) * (this.posY - (this.width / 2)) ) {

				this.over = true;
				
			if(mousePressed){
				
				this.posX = mouseX;
				this.posY = mouseY;
				this.vx = this.posX - this.prevPosX;
				this.vy = this.posY - this.prevPosY;
				this.frcX = 0;
				this.frcY = 0;
				
			}
			
		} else {
			
			this.over = false;
			
		}
		
    }

};
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
	
	function roundedRect(x, y, width, height, radius, c){

		c.beginPath();
		c.moveTo(x,y+radius);
		c.lineTo(x,y+height-radius);
		c.quadraticCurveTo(x,y+height,x+radius,y+height);
		c.lineTo(x+width-radius,y+height);
		c.quadraticCurveTo(x+width,y+height,x+width,y+height-radius);
		c.lineTo(x+width,y+radius);
		c.quadraticCurveTo(x+width,y,x+width-radius,y);
		c.lineTo(x+radius,y);
		c.quadraticCurveTo(x,y,x,y+radius);
		
//		this.ctx.noStroke();
	
	};
	
	function map(position, min1, max1, min2, max2) {
	    return min2 + (max2 - min2) * ((position - min1) / (max1 - min1));
	}
	