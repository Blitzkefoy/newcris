<div class="contact-section">
    <div class="contact-form">
        <h2>Contact Us</h2>
        <p>Address: Purok 4-A, Barangay Peñaplata, Island Garden City of Samal</p>
        <p>Phone: 0927-123-4567</p>
        <p>Email: crisinnresort@gmail.com</p>
        
        <form>
            <label for="name">Your Name:</label>
            <input type="text" id="name" name="name">
            
            <label for="email">Your Email:</label>
            <input type="email" id="email" name="email">
            
            <label for="message">Message:</label>
            <textarea id="message" name="message"></textarea>
            
            <button type="submit">Send</button>
        </form>
    </div>
    
    <div class="map-container" id="map">
        <!-- Map will be initialized here -->
    </div>
</div>
<script>
    function initMap() {
        var location = {lat: 7.071173, lng: 125.7118645};
        var map = new google.maps.Map(document.getElementById('map'), {
            zoom: 18,
            center: location
        });
        var marker = new google.maps.Marker({
            position: location,
            map: map
        });
    }
</script>
<script async defer
    src="https://maps.googleapis.com/maps/api/js?key=AIzaSyAMRRVJuz0gtou8Lgc_w_kINK3c0oimPGQ&callback=initMap">
</script>