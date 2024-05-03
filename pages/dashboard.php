<?php include 'includes/sessionconnection.php'; ?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sleep.io Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
   
    <link rel="stylesheet" type="text/css" href="styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <style>
        body {
            margin: 0;
            padding: 0;
            background-image: url('includes/images/MoonSkyBackground.webp');
            background-attachment: fixed; /* This makes the background image fixed */
            background-position: center; /* Centers the image in the viewport */
            background-repeat: no-repeat; /* Prevents the image from repeating */
            background-size: cover; /* Covers the entire viewport */
            
        }
        
        /* Add styles for your content and sections here */
        .section {
           
            position: relative; /* content is positioned over the background */
            z-index: 1; /* Higher than the default z-index of 0 */
            
        }
    </style>
</head>
<body>

    <!-- Navigation Bar -->
    <?php  include 'includes/nav.php'; ?>

    <!-- Introduction section -->
    <section class="h-screen w-screen relative">
    
    <div class="absolute inset-0 bg-black bg-opacity-50">
        <div class="flex justify-center items-center h-full">
            <div class="text-center">
                <h2 class="text-white text-4xl font-bold mb-4 fade-in">Welcome to <b>Sleep.io</b></h2>
                <p class="text-white mb-6 fade-in">Your sleep tracker and analyst</p>
                <h3 class="text-white text-xl font-bold mb-1 fade-in">Emmett Murray</h3>
                <p class="text-white fade-in">Lead Developer</p>
            </div>
        </div>
    </div>
</section>

    <!-- Main Content -->
 
      <Section class="h-screen w-screen relative "> 
      
      <div class="max-w-screen-lg mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-x-40 gap-y-8 text-center">
            <!-- First Feature -->
            <div class="flex flex-col items-center feature-fade-in">
                <img src="includes/images/Icon-Sleepy.png" alt="Track Sleep Patterns" class="mb-4 " >
                <h3 class="text-xl font-bold mb-2 text-white">Track Sleep Patterns</h3>
                <p class="text-white">A simple, smooth new way in tracking your sleep schedule.</p>
            </div>
            
            <!-- Second Feature -->
            <div class="flex flex-col items-center feature-fade-in">
                <img src="includes/images/attempt.png" alt="Analyze Results" class="mb-4">
                <h3 class="text-xl font-bold mb-2 text-white">Analyze Results</h3>
                <p class="text-white">Recieve detailed analysis on your sleep trends and patterns.</p>
            </div>
            
            <!-- Third Feature -->
            <div class="flex flex-col items-center feature-fade-in">
                <img src="includes/images/research.png" alt="Up-to-Date Sleep Research" class="mb-4">
                <h3 class="text-xl font-bold mb-2 text-white">Relevant Sleep Research</h3>
                <p class="text-white">Relevant research provided along side sleep insights.</p>
            </div>
        </div>
        <div class="flex justify-center mt-8">
            <a href="tracker.php"class="bg-yellow-500 text-black px-6 py-3 rounded hover:bg-yellow-600 transition duration-300">Click here to access Sleep Tracker</a>
        </div>
    </div>


      </Section>
      <section class="h-screen w-screen relative">  
    <!-- Main Content -->
 
        <!-- Icon Buttons Column -->
       
<div class="flex-2 grid grid-cols-2 md:grid-cols-5 gap-4 p-8 text-center feature-fade-in">
    
  
    <!-- Icon 1 -->
    <a href="history.php" class="icon-container flex flex-col justify-center items-center p-4 rounded-lg bg-gradient-to-r from-blue-500 to-teal-400" href="#">
        <i class="fas fa-history fa-3x text-white mb-2"></i>
        <p class="text-white text-lg">Sleep History</p>
</a>
    <!-- Icon 2 -->
    <a href="tracker.php" class="icon-container flex flex-col justify-center items-center p-4 rounded-lg bg-gradient-to-r from-blue-500 to-teal-400">
        <i class="fas fa-book-open fa-3x text-white mb-2"></i>
        <p class="text-white text-lg">tracker</p>
</a>

    <!-- Icon 3 -->
    <a href="profile.php" class="icon-container flex flex-col justify-center items-center p-4 rounded-lg bg-gradient-to-r from-blue-500 to-teal-400" href="#">
        <i class="fas fa-user-circle fa-3x text-white mb-2"></i>
        <p class="text-white text-lg">Profile</p>
</a>
    <!-- Icon 4 -->
    <a href="registration.php" class="icon-container flex flex-col justify-center items-center p-4 rounded-lg bg-gradient-to-r from-blue-500 to-teal-400">
        <i class="fas fa-clipboard fa-3x text-white mb-2"></i>
        <p class="text-white text-lg">Register</p>
</a>
    <!-- Icon 5 -->
    <a href="https://localhost/phpmyadmin/index.php?route=/database/structure&db=emurray46" class="icon-container flex flex-col justify-center items-center p-4 rounded-lg bg-gradient-to-r from-blue-500 to-teal-400" target="_blank>
        <i class="fas fa-screwdriver-wrench fa-3x text-white mb-2"></i>
        <p class="text-white text-lg">other</p>
</a>
   
</div>


        

  
   
</Section>

 
   







    <script>
         window.addEventListener('DOMContentLoaded', (event) => {
    const sections = document.querySelectorAll('section');
    const options = { rootMargin: '0px', threshold: 0.25 };
    
    const fadeInObserver = new IntersectionObserver((entries, observer) => {
        entries.forEach(entry => {
            if(entry.isIntersecting) {
                entry.target.querySelectorAll('.fade-in').forEach((el) => {
                    el.style.animationPlayState = 'running';
                });
            }
        });
    }, options);

    sections.forEach(section => {
        fadeInObserver.observe(section);
    });
});

document.addEventListener('DOMContentLoaded', (event) => {
  const observer = new IntersectionObserver((entries, observer) => {
    entries.forEach((entry, index) => {
      if (entry.isIntersecting) {
        // Add a delay based on the element's index
        entry.target.style.transitionDelay = `${index * 0.3}s`;
        entry.target.classList.add('feature-visible');
        observer.unobserve(entry.target); // Unobserve the element after it becomes visible
      }
    });
  }, {
    threshold: 0.1
  });

  const features = document.querySelectorAll('.feature-fade-in');
  features.forEach((feature, index) => {
    observer.observe(feature);
  });
});



    </script>

    

</body>
</html>


