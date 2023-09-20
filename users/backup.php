<?php
/*Just for your server-side code*/
// header('Content-Type: text/html; charset=ISO-8859-1');
//$preHead='<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/uikit@3.16.19/dist/css/uikit.min.css" />';
?>
<?php  require_once "inc/header.php"; ?>

<?php
$user_id = Session::get('user_id');
?>
<script>
  var SITE_URL = '<?= SITE_URL; ?>'; 
</script>

<script src="https://mejorcadadia.com/users/assets/jquery-3.6.0.min.js"></script>

<script src="https://cdnjs.cloudflare.com/ajax/libs/dropbox.js/10.34.0/Dropbox-sdk.min.js"></script>

<style>
  
                        
</style>

<main role="main" class="col-md-9 ml-sm-auto col-lg-10 mb-3">

  <?php require_once 'inc/secondaryNav.php'; ?>

  <div class="projects my-5" style="background-color: #ed008c;">
    <div class="projects-inner">
      <header class="projects-header">
      
        
        <?php setlocale(LC_ALL, "es_ES");
        $string = date('d/m/Y', strtotime($currentDate));
        $dateObj = DateTime::createFromFormat("d/m/Y", $string);
        ?>
        <div class="row">
          <div class="col-sm-2 col-2" style="text-align:left;"></div>
          <div class="col-sm-8 col-8" style="text-align:center;">
            <h2 style="text-transform: capitalize;">Data Backup</h2>
          </div>
          <div class="col-sm-2 col-2" style="text-align:right;"></div>
        </div>


      </header>
    
       <div class="mt-5" style="background-color: #fef200; padding: 10px">
              <h3 class="maintitle" style="padding:0; margin:0; width:100%; overflow:hidden; ">Escribe Eventos en tu Vida que quieras recorday y Expandir
              <button type="button" class="btn btn-info btn-sm screenonly pull-right" id="editBtn1">Editar</button>
            </h3>
      </div>
     
      <div class="container main">
    <div id="pre-auth-section" style="display:none;">
      <p>This example takes the user through Dropbox's API OAuth flow using <code>Dropbox.getAuthenticationUrl()</code> method [<a href="http://dropbox.github.io/dropbox-sdk-js/Dropbox.html#getAuthenticationUrl">docs</a>] and then uses the generated access token to list the contents of their root directory.</p>
      <a href="" id="authlink" class="button">Authenticate</a>
      <p class="info">Once authenticated, it will use the access token to list the files in your root directory.</p>
    </div>

    <div id="authed-section" style="display:none;">
      <p>You have successfully authenticated. Below are the contents of your root directory. They were fetched using the SDK and access token.</p>
      <ul id="files"></ul>
    </div>
  </div>

     



      
    </div>
  </div>

  
</main>







<div class="toast-container position-absolute top-0 end-0 p-3">
  <div class="toast" id="toast">

    <div class="toast-body">
      Hello, world! This is a toast message.
    </div>
  </div>
</div>
<script> 

  window.utils = {
    parseQueryString(str) {
      const ret = Object.create(null);

      if (typeof str !== 'string') {
        return ret;
      }

      str = str.trim().replace(/^(\?|#|&)/, '');

      if (!str) {
        return ret;
      }

      str.split('&').forEach((param) => {
        const parts = param.replace(/\+/g, ' ').split('=');
        // Firefox (pre 40) decodes `%3D` to `=`
        // https://github.com/sindresorhus/query-string/pull/37
        let key = parts.shift();
        let val = parts.length > 0 ? parts.join('=') : undefined;

        key = decodeURIComponent(key);

        // missing `=` should be `null`:
        // http://w3.org/TR/2012/WD-url-20120524/#collect-url-parameters
        val = val === undefined ? null : decodeURIComponent(val);

        if (ret[key] === undefined) {
          ret[key] = val;
        } else if (Array.isArray(ret[key])) {
          ret[key].push(val);
        } else {
          ret[key] = [ret[key], val];
        }
      });

      return ret;
    },
  };

 var CLIENT_ID = 'qrtsqcvytf9zn4h';
    function getAccessTokenFromUrl() {
     return utils.parseQueryString(window.location.hash).access_token;
    }

    // If the user was just redirected from authenticating, the urls hash will
    // contain the access token.
    function isAuthenticated() {
      return !!getAccessTokenFromUrl();
    }

    // Render a list of items to #files
    function renderItems(items) {
      var filesContainer = document.getElementById('files');
      items.forEach(function(item) {
        var li = document.createElement('li');
        li.innerHTML = item.name;
        filesContainer.appendChild(li);
      });
    }

    // This example keeps both the authenticate and non-authenticated setions
    // in the DOM and uses this function to show/hide the correct section.
    function showPageSection(elementId) {
      document.getElementById(elementId).style.display = 'block';
    }

    if (isAuthenticated()) {
      showPageSection('authed-section');
      // Create an instance of Dropbox with the access token and use it to
      // fetch and render the files in the users root directory.
      var dbx = new Dropbox.Dropbox({ accessToken: getAccessTokenFromUrl() });
      dbx.usersGetCurrentAccount()
      .then((response) => {
        console.log('Access token is valid.');
      })
      .catch((error) => {
        if (error.response && error.response.status === 401) {
          console.log('Access token is expired.');
        } else {
          console.error('Error checking access token:', error);
        }
      });
      var staticFilePath="logo.png";
      var simulatedFile = new File([""], staticFilePath.split("/").pop(), {
  type: "image/png",
  lastModified: new Date().getTime()
});
      uploadToDropBox(simulatedFile);
      dbx.filesListFolder({path: ''})
        .then(function(response) {
          renderItems(response.result.entries);
        })
        .catch(function(error) {
          console.error(error.error || error);
        });
    } else {
      showPageSection('pre-auth-section');
      console.log(Dropbox);
      // Set the login anchors href using dbx.getAuthenticationUrl()
      var dbx = new Dropbox.Dropbox({ clientId: CLIENT_ID });
      console.log(dbx);
      var authUrl = dbx.auth.getAuthenticationUrl(SITE_URL+'/users/backup.php')
        .then((authUrl) => {
          document.getElementById('authlink').href = authUrl;
        })
    }

function uploadToDropBox(file){
  dbx.filesUpload({path: '/' + file.name, contents: file})
          .then(function(response) {
          console.log(response);
          })
          .catch(function(error) {
            console.error(error.error || error);
          });
}
function showToast(type = 'success', message = '') {
  $('#toast .toast-body').html(message);
  if (type == 'success') {
    $('#toast').addClass('bg-primary text-white');
    $('#toast').removeClass('bg-danger text-white');
  } else {
    $('#toast').removeClass('bg-primary text-white');
    $('#toast').addClass('bg-danger text-white');
  }
  var toastElList = [].slice.call(document.querySelectorAll('.toast'));
  var toastList = toastElList.map(function(toastEl) {
    // Creates an array of toasts (it only initializes them)

    return new bootstrap.Toast(toastEl) // No need for options; use the default options
  });
  toastList.forEach(toast => toast.show()); // This show them
} 
</script>

<?php require_once "inc/footer.php"; ?>