<template>
       <section class="contact-form">
    <h1 class="heading">Get in Touch with Us!</h1>
    <p class="para">We are here to answer your questions and assist you in joining our luxury car auctions. Don't hesitate to reach out for any support.</p>
    
    <div class="contactForm">
        <form action="#">
         <h1 class="sub-heading">Need Assistance?</h1>
         <p class="para para2">Contact us for a quote or help with joining our exclusive auctions.</p>
         <input type="text" class="input" placeholder="your name">
         <input type="text" class="input" placeholder="your email">
         <input type="text" class="input" placeholder="your Subject">
         <textarea class="input" cols="30" rows="5" placeholder="Your message..."></textarea>
         <input type="submit" class="input submit" value="Send Message">
        </form>

        <div class="map-container">
            <div class="mapBg"></div>
            <div class="map">
                <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d2624.9914410252845!2d2.291906375932066!3d48.85837360070842!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x47e66e2964e34e2d%3A0x8ddca9ee380ef7e0!2sEiffel%20Tower!5e0!3m2!1sen!2set!4v1695627657575!5m2!1sen!2set" width="600" height="450" style="border:0;" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
            </div>
        </div>
    </div>

    <div class="contactMethod">
        <div class="method">
            <div class="text">
                <i class="bi bi-geo-fill"></i>
                <h1 class="sub-heading">Location</h1>
                <p class="para">Syria </p>
            </div>
        </div>

        <div class="method">
            <div class="text">
                <i class="bi bi-envelope-fill"></i>
                <h1 class="sub-heading">Email</h1>
                <p class="para">Email:mayabr@gmail.com</p>
            </div>
        </div>

        <div class="method">
            <div class="text">
                <i class="bi bi-telephone-fill"></i>
                <h1 class="sub-heading">Phone</h1>
                <p class="para">+963 9541646</p>
            </div>
        </div>
    </div>
   </section>
</template>
<style scoped>
@import url('https://fonts.googleapis.com/css2?family=Roboto+Serif:opsz,wght@8..144,400;8..144,500;8..144,600;8..144,700&display=swap');

*{
    margin: 0;
    padding: 0;
    text-decoration: none;
    
    color:  #000 !important;
}
.text {
    height: 114px !important;
    width: 258px !important;
    display: flex !important;
    flex-direction: column !important;
    align-items: center !important;
    text-align: center;
    border: 2px solid #990000;
    border-radius: 10px;
    margin: 0 10px;
    font-size: 18px;
    background-color: rgba(255, 255, 255, 0.8);
    color: #333;
}
:root{
    --bodyBg: linear-gradient(to right, #000000, #867e7e);
    --formBg:#ffffff;
    --textColor:#fff;
    --primaryColor:#EAC452;
    --paraColor:#ffffff;
    --paraColor:#ffffff;

}

body{
    font-family: 'Roboto Serif', serif;
    color: var(--textColor);
    background: var(--bodyBg);
    padding-bottom: 5em;
}

header{
   
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
    color:  #000 !important;
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
.contact-form{
    margin-top: 80px;
}

section{
    display: flex;
    flex-direction: column;
    text-align: center;
    width: 90%;
    max-width: 550px;
    margin: auto;
    margin-top: 2em;
}

.sectionHeader{
    text-transform: capitalize;
    font-weight: bold;
    font-size: 1.1rem;
    color: var(--primaryColor);
    margin-bottom: .5em;
}

.heading,.sub-heading{
    margin-bottom: .5em;
    font-weight: bold;
}

.heading{
    font-size: 2.5em;
}

.sub-heading{
    text-align: left;
}

.contactForm{
    display: grid;
    gap: 3em;
}

form{
    width: 100%;
    margin-top: 3em;
}

.para{
    color: var(--paraColor);
    font-size: 1.1rem;
    line-height: 1.5em;
    margin-bottom: 1em;
}

.para2{
    text-align: left;
}

.input{
    width: 95%;
    max-width: 700px;
    border: none;
    font-size: .9rem;
    padding: 1em;
    outline: none;
    background-color: var(--formBg);
    color: var(--paraColor);
    border-radius: 10px;
    border: 1px solid rgb(53, 53, 53);
    margin-bottom: 1em;

}

.input:focus{
    border: 1px solid var(--primaryColor);
}

.input::placeholder{
    text-transform: capitalize;
}

.submit{
    background-color: var(--primaryColor);
    border: none;
    color: var(--textColor);
    font-weight: bold;
    cursor: pointer;
    width: 100%;
}

.map-container{
    position: relative;
    width: 100%;
    height: 500px;
}

.mapBg{
    position: absolute;
    background-color: var(--primaryColor);
    top: 0;
    right: 0;
    width: 200px;
    height: 90%;
    border-radius: 20px;
}

.map{
    position: absolute;
    bottom: 0;
    left: 0;
    width: 90%;
    height: 90%;
}

.map iframe{
    width: 100%;
    height: 100%;
}

.contactMethod{
    display: flex;
    flex-direction: column;
    margin-top: 2em;
    text-align: left;
}

.method{
    display: flex;
    align-items: baseline;

}
.method i{
    color: #ffffff;
}
.text{
    margin-left: 20px;
}

.contactIcon{
    font-size: 2rem;
    color: var(--primaryColor);
    width: 70px;
}

@media screen and (min-width:800px) {
    section{
        max-width: 1100px;
    }
    .contactForm{
        grid-template-columns: 1fr 1fr;
    }   

    .contactMethod{
        flex-direction: row;
        justify-content: space-between;
    }
    
}
</style>