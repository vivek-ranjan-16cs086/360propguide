<div class="menuBack vh-100 vw-100 menuHide menuOpen fixed-top overflow-hidden">

  <div class="close position-absolute CustomIconPosition">
    <i class="fa-solid fa-circle-xmark customHideIcon"></i>
  </div>
  <div class="d-flex flex-column side-width h-100">
  <div class="fw-bolder world_space ms-2 ms-md-5  mt-5 h2 sideMenuBorder pb-2 customLetterSpace ">TAKE ME
  TO </div>
    <div class=" h-75 ms-2 ms-md-5 overflow-auto">
      
        <a class="custom-border-bottom  p-2 mb-2 w-100 mt-4 fw-bold hvr d-block" href="{{url('/')}}">Welcome Aboard</a>
        <a class="custom-border-bottom p-2 mb-2 w-100 fw-bold hvr d-block" href="{{url('/about-us')}}">Our Passion</a>
        <a class="custom-border-bottom  p-2 mb-2 w-100 mt-4 fw-bold hvr d-block" href="{{url('/projects')}}">Explore Projects</a>
		<a class="custom-border-bottom  p-2 mb-2 w-100 mt-4 fw-bold hvr d-block" href="{{url('/properties')}}">Explore Properties</a>
        <a class="custom-border-bottom  p-2 mb-2 w-100 mt-4 fw-bold hvr d-block" href="{{url('/blogs')}}">360 Knowledge Base</a>
        <a class="custom-border-bottom  p-2 mb-2 w-100 mt-4 fw-bold hvr d-block" href="{{url('/careers')}}">Join Our Journey</a>
        <a class="custom-border-bottom  p-2 mb-2 w-100 mt-4 fw-bold hvr d-block" href="{{url('/contact')}}">Get in Touch</a>
      

    </div>
  </div>

</div>
<script>
  $(document).ready(function() {
    $(".close").click(function() {
      $(".menuHide").css(
        "top", "-100vh"


      )
    });
  });
  $(document).ready(function() {
    $(".open").click(function() {
      $(".menuOpen").css(
        "top", "0"
      )
    });
  });
</script>