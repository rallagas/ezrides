

    const canvas = document.getElementById("motorcycleCanvas");
    const ctx = canvas.getContext("2d");

    // Motorcycle and rider properties
    let xPosition = 0; // Initial x position
    const yPosition = 200; // Fixed y position

    function drawRider() {
        // Draw the body (purple shirt)
        ctx.fillStyle = "purple";
        ctx.fillRect(xPosition + 40, yPosition - 30, 20, 30); // Body
        
        // Draw the helmet (yellow)
        ctx.beginPath();
        ctx.arc(xPosition + 50, yPosition - 40, 10, 0, Math.PI * 2); // Head
        ctx.fillStyle = "yellow";
        ctx.fill();
        ctx.closePath();
    }

    function drawMotorcycle() {
        // Draw the wheels
        ctx.beginPath();
        ctx.arc(xPosition + 30, yPosition + 20, 20, 0, Math.PI * 2); // Rear wheel
        ctx.arc(xPosition + 90, yPosition + 20, 20, 0, Math.PI * 2); // Front wheel
        ctx.fillStyle = "black";
        ctx.fill();
        ctx.closePath();

        // Draw the motorcycle body
        ctx.beginPath();
        ctx.moveTo(xPosition + 30, yPosition + 20); // Rear wheel center
        ctx.lineTo(xPosition + 70, yPosition - 10); // Seat area
        ctx.lineTo(xPosition + 90, yPosition + 20); // Front wheel center
        ctx.lineWidth = 4;
        ctx.strokeStyle = "gray";
        ctx.stroke();
        ctx.closePath();

        // Draw the handlebar
        ctx.beginPath();
        ctx.moveTo(xPosition + 90, yPosition + 20);
        ctx.lineTo(xPosition + 110, yPosition - 10); // Handlebar
        ctx.lineWidth = 2;
        ctx.stroke();
        ctx.closePath();
    }

    function drawScene() {
        ctx.clearRect(0, 0, canvas.width, canvas.height); // Clear the canvas

        // Draw the ground
        ctx.fillStyle = "green";
        ctx.fillRect(0, yPosition + 40, canvas.width, canvas.height - yPosition - 40);

        // Draw the motorcycle and rider
        drawMotorcycle();
        drawRider();

        // Update position
        xPosition += 2;

        // Loop the animation
        if (xPosition > canvas.width) {
            xPosition = -100; // Reset position to create a loop effect
        }

        requestAnimationFrame(drawScene); // Continue animation
    }

    drawScene(); // Start animation
