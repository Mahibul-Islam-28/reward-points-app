<section class="footer-section bg-light">
     <footer>
          <div class="col d-flex justify-content-center pt-3 list-unstyled">
               <li><a href="{{route('about')}}">About</a></li>
               <li><a href="{{route('termsPrivacy')}}">Terms & Privacy</a></li>
               <li><a href="{{route('appDownload')}}">Download</a></li>
          </div>
          <div class="row text-center py-3">
               <small>Copyright &copy;{{ now()->year }} Wexprez</small>
          </div>
     </footer>
</section>