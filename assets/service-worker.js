self.addEventListener('beforeinstallprompt', (event) => {
    event.preventDefault(); // Prevent the default browser prompt
    const deferredPrompt = event;
  
    // Listen for the user's choice to install or dismiss the app
    self.addEventListener('message', (event) => {
      if (event.data === 'installApp') {
        deferredPrompt.prompt(); // Show the browser prompt
      }
    });
  
    // Respond with the installation status to the web app
    self.addEventListener('appinstalled', (event) => {
      self.clients.get(event.clientId).then((client) => {
        client.postMessage('appInstalled');
      });
    });
  });