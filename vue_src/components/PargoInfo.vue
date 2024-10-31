<template>
  <div class="p-pargo-info">
    <p>
      <a class="button button-pargo" href="https://mypargo.pargo.co.za/mypargo" style="" @click="doEvent"
          target="_blank"><b>Login</b>
      to your myPargo account</a>
    </p>
    <!--create two equal divs -->

    <div class="item">
      <h2>South Africa</h2>
      <p><b>Email Pargo:</b> <a href="mailto:info@pargo.co.za">info@pargo.co.za</a></p>
      <p><b>Call Pargo:</b> <a href="tel:+27 21 447 3636"> +27 21 447 3636  </a></p>
      <p><b>About Pargo:</b> <a href="https://pargo.co.za/faq/" target="_blank">Frequently Asked Questions</a></p>
    </div>
    <div class="item">
      <h2>Egypt</h2>
      <p><b>Email Pargo:</b> <a href="mailto:support.eg@pargo.com">support.eg@pargo.com</a></p>
      <p><b>Call Pargo:</b> <a href="tel:+20 100 326 5919"> +20 100 326 5919  </a> </p>
      <p><b>About Pargo:</b> <a href="https://eg.pargo.com/faq/" target="_blank">Frequently Asked Questions</a></p>
    </div>
  </div>
</template>

<script>
export default {
  name: 'PargoInfo',
  methods: {
    doEvent: async () => {
      await fetch(`${OBJ.api_url}pargo/v1/get-signup-click-event`, {
          headers: {
          'X-WP-Nonce': OBJ.nonce,
        }
      }).then(response => console.log(response)).catch(error => console.error(error));
    }
  }
}
</script>

<style scoped>
.item {
  width: 100%;
}
.p-pargo-info {
  display: flex;
  flex-direction: column;
  border: thin solid;
  border-radius: 0.4rem;
  background-color: #FFF;
}

.button.button-pargo {
  background-color: #FFF200;
  box-shadow: 0 1px 2px rgba(0, 0, 0, 0.195772);
  border-radius: 4px;
  border: none;
  color: #000;
  padding: 0.2rem 2rem;
  text-transform: uppercase;
  font-weight: bold;
}
.button.button-pargo:hover {
  background-color: #DDD465;
  box-shadow: 0 1px 2px rgba(0, 0, 0, 0.5);
}

p {
  margin: 0;
  padding: 0.5rem;
}

@media (min-width: 1024px) {
  .p-pargo-info {
    flex-direction: row;
  }
  .item {
    width: 40%;
  }
}
</style>
