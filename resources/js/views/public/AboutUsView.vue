<template>
    <main class="header">
        <h1 class="main-heading">About Us</h1>
        <p class="sub-heading">Welcome to the ultimate luxury car auction destination!</p>
    </main>

    <!-- Introduction Section -->
    <section class="intro">
        <div class="container">
            <h2 class="section-heading">Our Story</h2>
            <p class="section-para">Founded with a passion for luxury and a commitment to excellence, we are dedicated
                to providing an exclusive platform for buyers and sellers of luxury and high-performance vehicles. Our
                mission is to connect enthusiasts and collectors with the cars they’ve always dreamed of.</p>
        </div>
    </section>

    <!-- Services Section -->
    <section class="services">
        <div class="container">
            <h2 class="section-heading">What We Offer</h2>
            <ul class="services-list">
                <li>Exclusive auctions of the world’s finest luxury cars</li>
                <li>Transparent and secure bidding process</li>
                <li>Personalized support for buyers and sellers</li>
                <li>Comprehensive vehicle information and history</li>
            </ul>
        </div>
    </section>



    <!-- Values Section -->
    <section class="values">
        <div class="container">
            <h2 class="section-heading">Our Values</h2>
            <p class="section-para">Integrity, transparency, and a passion for luxury are the core values that guide our
                work. We believe in creating an environment where trust and excellence are at the forefront of every
                transaction.</p>
        </div>
    </section>
</template>
<style scoped>
/* Reset some default styles */
* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
    font-family: Arial, sans-serif;
}

body {
    background-color: #f5f5f5;
    color: #333;
    line-height: 1.6;
}

/* Header styling */
.container{
    flex-direction: column;
}

header{
    color: #f5f5f5 ;
    margin:auto;
    max-width: 80%;
    display: flex;
    justify-content: center;
    position: relative;
    z-index: 500;
    padding-top: 10px;
}
.nav{
    display: flex;
    top: -1%;
    align-items: baseline;
    justify-content: space-between;
    background-color: #000000;
    width:70%;
    position: absolute;
    position: fixed;
    padding: 15px 50px 10px;
    border-radius: 10px;
}


/* ////////////////////////// */
header .nav .Reminder{
   
    /* position: absolute;  */
    font-size: 20px;
  
}
header .nav .Reminder i{
    cursor: pointer;
    color: whitesmoke;
}
header .nav .header-icon{
    color:  #fdf8f8; 
    font-size: 30px;
    display: none;
    cursor: pointer;
 
}
header .nav .logo{
    font-weight: bold;
    font-size: 30px;
}
header  .nav .menu{
    padding: 0;
    margin: 0;
    list-style: none;
    display: flex;
    justify-content: center;
    gap: 20px;
    font-weight: 500;
}
header .nav .menu li a:hover{
    scale: 1.05;
    color: #EAC452;
}
header .nav .side-list{
    display: none;
}
.menu li a{
    list-style-type: none;
    text-decoration: none;
    color:  #fdf8f8;
    transition: scale 0.2 ease;
    cursor: pointer;
    transition: .5s;
}
.menu li a:hover{
   
    scale: 1.20;
}
.menu li a:active{
    scale: 0.95;
}
header .nav .btn{
    width: 200px;
    /* background-color: #6b0099; */
    /* margin-top: 10px; */
    /* padding-left: 0px; */
    margin-right: -150px;
    margin-bottom: 5px;
    
    /* background-color: #6b0099; */
}
header .nav .btn1{
    text-align: center;
    width: 80px;
    height: 40px;
    border: none;
     margin-left: 12px; 
    border-radius: 15px;
    font-size: 15px;
    color:  #000000;
    background-color: rgb(255, 255, 255);
    transition: scale 0.2 ease;
    cursor: pointer;
    transition: .5s;


}
.btn1:hover{
    background-color: #ffffff;
    color: #000;
    scale: 1.05;
}
.btn1:active{
    scale: 0.95;
}

 /* dropdown menu */
 header .nav .dropdown_menu{
   display: none;

}
.header {
    background-color: #000000;
    color: #fff;
    text-align: center;
    padding: 3rem 0;
    animation: fadeIn 1s ease-in-out;
    margin-top: 5%;
}

.main-heading {
    font-size: 2.8rem;
    font-weight: bold;
    margin-bottom: 0.5rem;
    animation: slideDown 1s ease-in-out;
}

.sub-heading {
    font-size: 1.3rem;
    font-weight: 300;
    opacity: 0.9;
}

/* Section styling */
.container {
    width: 80%;
    max-width: 1200px;
    margin: 0 auto;
    padding: 3rem 0;
    animation: fadeIn 1.2s ease-in-out;
}

.section-heading {
    font-size: 2.4rem;
    color: #222;
    margin-bottom: 1.5rem;
    text-align: center;
    position: relative;
}

.section-heading::after {
    content: "";
    display: block;
    width: 80px;
    height: 4px;
    background-color: #ffdd57;
    margin: 1rem auto;
    animation: growLine 1.2s ease-in-out;
}

.section-para {
    font-size: 1.1rem;
    color: #666;
    text-align: center;
    margin-bottom: 2rem;
    line-height: 1.8;
}

/* Services section */
.services-list {
    list-style-type: none;
    padding: 0;
    text-align: center;
    font-size: 1.1rem;
    color: #333;
    animation: fadeInUp 1.3s ease-in-out;
}

.services-list li {
    margin: 0.5rem 0;
    transition: transform 0.3s;
}

.services-list li:hover {
    transform: translateX(10px);
    color: #ff8800;
}

/* Team section */
.team-gallery {
    display: flex;
    justify-content: space-around;
    flex-wrap: wrap;
    gap: 2rem;
    animation: fadeInUp 1.3s ease-in-out;
}

.team-member {
    text-align: center;
    width: 250px;
    transform: scale(0.9);
    transition: transform 0.3s;
}

.team-member:hover {
    transform: scale(1);
}

.team-photo {
    width: 100%;
    height: 250px;
    object-fit: cover;
    border-radius: 50%;
    border: 4px solid #333;
    margin-bottom: 0.5rem;
    transition: transform 0.3s, box-shadow 0.3s;
}

.team-photo:hover {
    transform: scale(1.05);
    box-shadow: 0px 4px 20px rgba(0, 0, 0, 0.2);
}

.team-member h3 {
    font-size: 1.2rem;
    color: #333;
    margin-top: 0.5rem;
    animation: fadeIn 1.4s ease-in-out;
}

.team-member p {
    font-size: 0.9rem;
    color: #777;
    animation: fadeIn 1.4s ease-in-out;
}

/* Footer styling */
.footer {
    background-color: #000000;                       
    color: #fff;
    text-align: center;
    padding: 1.5rem 0;
    animation: fadeIn 1.5s ease-in-out;
}

.footer p {
    margin: 0.5rem 0;
}

.footer a {
    color: #ffdd57;
    text-decoration: none;
    transition: color 0.3s;
}

.footer a:hover {
    color: #ff8800;
}

/* Animations */
@keyframes fadeIn {
    from { opacity: 0; }
    to { opacity: 1; }
}

@keyframes slideDown {
    from { transform: translateY(-20px); opacity: 0; }
    to { transform: translateY(0); opacity: 1; }
}

@keyframes fadeInUp {
    from { transform: translateY(20px); opacity: 0; }
    to { transform: translateY(0); opacity: 1; }
}

@keyframes growLine {
    from { width: 0; }
    to { width: 80px; }
}

/* Responsive styling */
@media (max-width: 768px) {
    .team-gallery {
        flex-direction: column;
        align-items: center;
    }

    .team-member {
        width: 80%;
    }
   
}
</style>