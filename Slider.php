<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Slider Demo</title>
  <style>
    /* CSS cho Slider */

    /* Slider Container */
    .slider {
      position: relative;
      width: 100%;
      max-width: 1200px;
      margin: auto;
      overflow: hidden;
    }

    /* Slide Images */
    .slides {
      display: flex;
      transition: transform 0.5s ease-in-out;
    }

    .slide {
      min-width: 100%;
      box-sizing: border-box;
    }

    .slide img {
      width: 100%;
      display: block;
    }

    /* Navigation Buttons */
    .prev, .next {
      position: absolute;
      top: 50%;
      transform: translateY(-50%);
      background-color: rgba(0, 0, 0, 0.5);
      color: white;
      border: none;
      padding: 10px;
      cursor: pointer;
      z-index: 10;
    }

    .prev {
      left: 10px;
    }

    .next {
      right: 10px;
    }
  </style>
</head>
<body>
  <!-- Slider HTML -->
  <div class="slider">
    <div class="slides">
      <!-- Slide 1 -->
      <div class="slide">
        <img src="slide1.jpg" alt="Slide 1">
      </div>
      <!-- Slide 2 -->
      <div class="slide">
        <img src="slide2.jpg" alt="Slide 2">
      </div>
      <!-- Slide 3 -->
      <div class="slide">
        <img src="slide3.jpg" alt="Slide 3">
      </div>
    </div>
    <!-- Nút điều khiển -->
    <button class="prev" onclick="prevSlide()">&#10094;</button>
    <button class="next" onclick="nextSlide()">&#10095;</button>
  </div>

  <script>
    // JavaScript để tạo hiệu ứng trượt
    let currentIndex = 0;

    function showSlide(index) {
      const slides = document.querySelector('.slides');
      const totalSlides = document.querySelectorAll('.slide').length;

      if (index >= totalSlides) {
        currentIndex = 0; // Quay lại slide đầu tiên
      } else if (index < 0) {
        currentIndex = totalSlides - 1; // Đến slide cuối cùng
      } else {
        currentIndex = index;
      }

      const offset = -currentIndex * 100; // Tính toán vị trí trượt
      slides.style.transform = `translateX(${offset}%)`;
    }

    function nextSlide() {
      showSlide(currentIndex + 1);
    }

    function prevSlide() {
      showSlide(currentIndex - 1);
    }

    // Tự động chuyển slide
    setInterval(() => {
      nextSlide();
    }, 5000); // 5 giây
  </script>
</body>
</html>
