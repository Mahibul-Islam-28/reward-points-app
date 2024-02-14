<!DOCTYPE html>
<html lang="en">
     <head>
          <meta charset="UTF-8">
          <meta http-equiv="X-UA-Compatible" content="IE=edge">
          <meta name="viewport" content="width=device-width, initial-scale=1.0">
          <meta name="keywords" content = "WeXprez" />
          <meta name="description" content = "Anything and everything, don’t stay quiet – Just Xprez it on Wexprez." />
          <meta property="og:title" content="WeXprez" />
          <meta property="og:description" content="Anything and everything, don’t stay quiet – Just Xprez it on Wexprez" />
          <meta property="og:image" content="{{asset('')}}images/fb-meta.png" />
          <meta property="og:image:width" content="1200" />
          <meta property="og:image:height" content="630" />

          <title>@yield('title')</title>

          @include('layout.links')

          @yield('css')
     </head>
     <body>
          @include('layout.navbar')

          <div class="progress-wrap">
			<svg class="progress-circle svg-content" width="100%" height="100%" viewBox="-1 -1 102 102">
				<path d="M50,1 a49,49 0 0,1 0,98 a49,49 0 0,1 0,-98" />
			</svg>
		</div>
          
          @yield('content')

          @include('layout.footer')

          @include('layout.script')
          
          @yield('js')
     </body>
</html>