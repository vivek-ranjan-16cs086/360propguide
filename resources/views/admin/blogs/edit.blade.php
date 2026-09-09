@extends('admin.app')

@section('title', $title)

@section('customCss')
<link rel="stylesheet" href="{{url('assets/customs/css/career.css')}}">
@endsection

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            {!! $breadcrumbHtml !!}

            <div class="contentCard projects">
                <div class="card">
                    <div class="card-body">

                        <form method="POST" id="myForm" action="{{route('blogs.update')}}" enctype="multipart/form-data">
                            @csrf
                            <input type="hidden" name="id" value="{{base64_encode($blog->id)}}">
                            <div class="row">
                                <div class="col-sm-12 col-lg-6 mb-4">
                                    <label for="title">Blog Title</span></label>
                                    <input class="form-control input @error('title') is-invalid @enderror " type="text"
                                        placeholder="Enter Blog title" value="{{old('title') ? old('title') : $blog->title}}"
                                        name="title" required>
                                </div>
                                <div class="col-sm-12 col-lg-6 mb-4">
                                    <label for="short_description">Short Description</span></label>
                                    <input class="form-control input @error('short_description') is-invalid @enderror "
                                        type="text" placeholder="Enter Short Description"
                                        value="{{old('short_description') ? old('short_description') : $blog->short_description}}"
                                        name="short_description" required>
                                </div>
                                <div class="col-sm-12 col-lg-6 mb-4">
                                    <label for="seo_data">Meta Keywords</label>
                                    <input
                                        class="form-control input @error('seo_data[primary_keyword]') is-invalid @enderror "
                                        type="text" placeholder="Enter Meta Keywords"
                                        value="{{old('seo_data[primary_keyword]') ? old('seo_data[primary_keyword]') : $blog->seo_data['primary_keyword']}}"
                                        name="seo_data[primary_keyword]" required>
                                </div>
                                <div class="col-sm-12 col-lg-6 mb-4">
                                    <label for="seo_data">SEO Title</label>
                                    <input
                                        class="form-control input @error('seo_data[secondary_keyword]') is-invalid @enderror "
                                        type="text" placeholder="Enter SEO Title"
                                        value="{{old('seo_data[secondary_keyword]') ? old('seo_data[secondary_keyword]') : $blog->seo_data['secondary_keyword']}}"
                                        name="seo_data[secondary_keyword]" required>
                                </div>
                                <div class="col-sm-12 col-lg-6 mb-4">
                                    <label for="seo_data">Meta Description</label>
                                    <input
                                        class="form-control input @error('seo_data[meta_description]') is-invalid @enderror "
                                        type="text" placeholder="Enter Blog Meta Description"
                                        value="{{old('seo_data[meta_description]') ? old('seo_data[meta_description]') : $blog->seo_data['meta_description']}}"
                                        name="seo_data[meta_description]" required>
                                </div>

                                <div class="col-sm-12 col-lg-6 mb-4">
                                    <label for="canonical_link">Canonical Link</label>
                                    <input
                                        class="form-control input @error('seo_data[canonical_link]') is-invalid @enderror "
                                        type="text" placeholder="Enter Canonical Link"
                                        value="{{@old('seo_data[canonical_link]') ? @old('seo_data[canonical_link]') : @$blog->seo_data['canonical_link']}}"
                                        name="seo_data[canonical_link]" required>
                                </div>

                                <div class="col-sm-12 col-lg-6 mb-4">
                                    <label for="alt_tag">Image Alt Tag</label>
                                    <input class="form-control input @error('seo_data[alt_tag]') is-invalid @enderror "
                                        type="text" placeholder="Enter Image Alt Tag"
                                        value="{{@old('seo_data[alt_tag]') ? @old('seo_data[alt_tag]') : @$blog->seo_data['alt_tag']}}"
                                        name="seo_data[alt_tag]" required>
                                </div>

                                <div class="col-sm-12 col-lg-6 mb-4">
                                    <label for="slug">Slug</label>
                                    <input class="form-control input @error('slug') is-invalid @enderror " type="text"
                                        placeholder="Enter Slug" value="{{@old('slug') ? @old('slug') : @$blog->slug}}"
                                        name="slug" id="slug" required>
										<span id="slug-status"></span> 
                                </div>



                                <div class="col-sm-12 col-lg-6 mt-3">
                                    <div class="form-group">
                                        <label for="description">Description:</label>
                                        <textarea name="description" id="blogSummernote" class="blogSummernote">
										{{@old('description') ? @old('description') : @$blog->description}}
										</textarea>
                                    </div>
                                </div>
                                <div class="col-sm-12 col-lg-6 d-flex mb-4">
                                    <div class="form-group fileInput mr-2">
                                        <label for="thumbnail">Featured Image:</label>
                                        <input type="file"
                                            class="form-control @error('featured_image') is-invalid @enderror"
                                            name="featured_image" onchange="previewImage(this, '#previewFeaturedImage')"
                                            placeholder="Project Featured Image" id="featured_image">
                                    </div>
                                    <div class="fileInput">
                                        <img src="{{url('storage/' . $blog->featured_image)}}" alt="photo"
                                            id="previewFeaturedImage">
                                    </div>
                                </div>
								<div class="col-12 mt-5">
									<h5>
										FAQs Section
									</h5>
								</div>
                                <div class="row p-3 faqs" style="width:100%">
								  @if(!empty(json_decode(@$blog->faqs_data)) &&
									count(json_decode(@$blog->faqs_data))>0)
								  @foreach(json_decode(@$blog->faqs_data) as $index =>
									$faqData)

									<div class="col-sm-12 col-lg-12 mt-2 faqData" style="padding:0 30px">
										<div class="row pt-5 pb-5" style="background:#1b577733">
											<div class="col-6">
												<label for="question">Question:</label>
												<input value="{{(@old('question')) ? (@old('question')) : (@$faqData->question)}}" name="faqs_data[{{@$index}}][question]" type="text" class="form-control" required>
											</div>
											<div class="col-6"> 
												<label for="answer">Answer:</label>
												<input  value="{{(@old('answer')) ? (@old('answer')) : (@$faqData->answer)}}"  name="faqs_data[{{@$index}}][answer]" type="text" class="form-control" required>
											</div>
                                        </div>
									    @if($index == 0)
										<span class="bg-warning plusBtnFAQ">
											<i class="fa fa-plus"></i>
										</span>
										@else
										<span class="bg-danger minusBtnFAQ">
											<i class="fa fa-minus"></i>
										</span>
										@endif
									</div>  
                                  @endforeach
								 @else
									 <div class="col-sm-12 col-lg-12 mt-2 faqData" style="padding:0 30px">
									 <div class="row pt-5 pb-5" style="background:#1b577733">
											<div class="col-6">
												<label for="question">Question:</label>
												<input  name="faqs_data[0][question]" type="text" class="form-control" required>
											</div>
											<div class="col-6"> 
												<label for="answer">Answer:</label>
												<input name="faqs_data[0][answer]" type="text" class="form-control" required>
											</div>
                                        </div>
										<span class="bg-warning plusBtnFAQ">
											<i class="fa fa-plus"></i>
										</span>
										</div>
								 @endif
								 
                                </div>
                                <div class="col-12">
                                    <input type="checkbox" name="status" checked="checked">
                                    Make this blog active.
                                </div>
                                <div class="col-12 mt-3">
                                    <button id="storeBtn" type="submit" class="btn btn-primary">Upload Blog</button> 
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('customJs')
<script>
    function previewImage(input, previewId) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();
            reader.onload = function (e) {
                $(previewId).attr('src', e.target.result);
            }
            reader.readAsDataURL(input.files[0]);
        }
    }
</script>

<script>
  //function to re-index the FAQs index
     function reIndexFaqsData() {
        $('.faqs .faqData').each(function(index) {
            // Update index in input names
            $(this).find('[name^="faqs_data"]').each(function() {
                let newName = $(this).attr('name').replace(/\[\d+\]/, `[${index}]`);
                $(this).attr('name', newName);
            });
        });
    }
    // plus button click event for the Faq data
    $('.plusBtnFAQ').click(function() {
        let currentIndex = $('.faqs .faqData').length; // Get the current number of floor plans

        let html = `<div class="col-sm-12 col-lg-12 mt-2 faqData" style="padding:0 30px">
                        <div class="row pt-5 pb-5" style="background:#1b577733">
                            
                            <div class="col-6">
                                <label for="question">Question:</label>
                                <input name="faqs_data[${currentIndex}][question]" type="text" class="form-control" required>
                            </div>
                            <div class="col-6">
                                <label for="answer">Answer:</label>
                                <input name="faqs_data[${currentIndex}][answer]" type="text" class="form-control" required>
                            </div>
                        </div>
                        <span class="bg-danger minusBtnFAQ">
                            <i class="fa fa-minus"></i>
                        </span>
                    </div>`;
        $('.faqs').append(html);
        reIndexFaqsData();
    });
    $(document).on('click', '.minusBtnFAQ', function() {
        $(this).closest('.faqData').remove();
        reIndexFaqsData();
    });
	
	$('#openModalBtn').click(function(){
      $('#myModal').modal('show');
    }); 
</script>
@endsection
