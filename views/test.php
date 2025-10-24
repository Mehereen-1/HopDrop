<!-- index.php -->
<?php include '../includes/header.php'; ?>

<section class="hero">
    <div class="overlay"></div>
    <div class="hero-text">
        <h2>Deliver Smiles Across Your City 🌆</h2>
        <p>CityLink lets you send, earn, or help — all within your community.  
        Deliveries made local, meaningful, and easy.</p>
        <div class="hero-buttons">
            <a href="#" class="btn primary">Request a Delivery</a>
            <a href="#" class="btn secondary">Join as a Partner</a>
        </div>
    </div>
</section>

<section class="features">
    <h3>Choose Your Way to Connect</h3>
    <div class="feature-list">
        <div class="feature-card">
            <h4>📦 Request a Delivery</h4>
            <p>Need to send something across town?  
            Our trusted network delivers quickly and safely.</p>
        </div>
        <div class="feature-card">
            <h4>🚴‍♂️ Earn While You Go</h4>
            <p>Turn your daily commute into extra income.  
            Be your own boss, one delivery at a time.</p>
        </div>
        <div class="feature-card">
            <h4>💚 Volunteer with Heart</h4>
            <p>Deliver essentials for those in need.  
            Small acts of kindness make big differences.</p>
        </div>
    </div>
</section>

<section class="community">
    <h3>Connecting People, One Delivery at a Time</h3>
    <p>Join a community that believes in helping, earning, and sharing the ride.  
    Every delivery carries a purpose — yours can too.</p>
    <a href="#" class="btn primary large">Get Started</a>
</section>

<footer>
    <p>© 2025 CityLink | Built with ❤️ for your city</p>
</footer>

</body>
</html>
<style>
    body {
    font-family: "Poppins", sans-serif;
    margin: 0;
    background: #fafafa;
    color: #333;
}

/* HERO */
.hero {
    display: flex;
    align-items: center;
    justify-content: center;
    text-align: center;
    background: url('images/city-bg.jpg') center/cover no-repeat;
    color: white;
    padding: 6rem 2rem;
    background-color: rgba(243, 87, 160, 0.6);
}
.hero h2 {
    font-size: 2rem;
}
.hero-buttons {
    margin-top: 1.5rem;
}
.btn {
    display: inline-block;
    padding: 0.7rem 1.5rem;
    border-radius: 25px;
    text-decoration: none;
    margin: 0 0.5rem;
    font-weight: 600;
}
.btn.primary {
    background: #52ab98;
    color: #fff;
}
.btn.secondary {
    background: #fff;
    color: #2b6777;
}

/* FEATURES */
.features {
    text-align: center;
    padding: 3rem 2rem;
}
.feature-list {
    display: flex;
    justify-content: center;
    gap: 2rem;
    flex-wrap: wrap;
}
.feature-card {
    background: #fff;
    padding: 1.5rem;
    border-radius: 10px;
    box-shadow: 0 4px 8px rgba(0,0,0,0.1);
    width: 250px;
}

/* FOOTER */
footer {
    text-align: center;
    padding: 1rem;
    background: #2b6777;
    color: #fff;
}
</style>