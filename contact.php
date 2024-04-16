<?php include 'header.php';?>

<main class="container">
    <h1>Contact Us</h1>

    <section class="contact-form">
        <h2>Send a Message</h2>
        <form action="submit-form-url" method="POST">
            <label for="name">Name:</label>
            <input type="text" id="name" name="name" required>

            <label for="email">Email:</label>
            <input type="email" id="email" name="email" required>

            <label for="subject">Subject:</label>
            <input type="text" id="subject" name="subject">

            <label for="message">Message:</label>
            <textarea id="message" name="message" required></textarea>

            <button type="submit">Send Message</button>
        </form>
    </section>
</main>


<?php include 'footer.php';?>