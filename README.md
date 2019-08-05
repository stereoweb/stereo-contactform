# Stereo Contact Form

Zero Config Contact Form with Logging for Wordpress

## Getting Started

Install the plugin with the informations specified here

### Prerequisites

Wordpress with Bedrock, jQuery loaded.

### Installing

```
composer require stereo/contact-form
```

## Usage

Create forms in raw html and add class "js-stereo-cf" to your form.

```
<form class="[any other classes] js-stereo-cf">
    <textarea name="message" required></textarea>
    <input type="submit">
</form>
<div style="display:none">Thanks.</div>
```

With this code, an email with the message content will be sent to the admin email of your Wordpress installation.

The subject will be "Formulaire de contact"

It will be logged in your Wordpress Administration, with the date, followed by the first field of your form.

### Want to change the email subject to better fit your needs?

```
<form class="[any other classes] js-stereo-cf" data-subject="Formulaire de contact de ma mère">
    <textarea name="message" required></textarea>
    <input type="submit">
</form>
<div style="display:none">Thanks.</div>
```

### Want to change the title field in the Wordpress Administration ?

Setting the data-title to a comma seperated list of fields will concatenate each with a space between.

```
<form class="[any other classes] js-stereo-cf" data-title="Prenom,Nom">
    <input name="Prenom" required>
    <input name="Nom" required>
    <input type="submit">
</form>
<div style="display:none">Thanks.</div>
```

### Want to set a category to filter the Administration list in case of multiple forms ?

The default category is Contact

```
<form class="[any other classes] js-stereo-cf" data-category="Subscription">
    <input name="Prenom" required>
    <input name="Nom" required>
    <input type="submit">
</form>
<div style="display:none">Thanks.</div>
```

### Want to redirect on success ?

Set data-redirect with the redirect url

```
<form class="[any other classes] js-stereo-cf" data-redirect="/success">
    <input name="Prenom" required>
    <input name="Nom" required>
    <input type="submit">
</form>
<div style="display:none">Thanks.</div>
```


### Manipulate the "TO" email address

If you want the email to go somewhere else :

```
add_filter('st_cf_mail_to',function() {
    return 'Your Mom <yourmom@gmail.com>';
});
```

### Manipulate the "FROM" email address

If you want the email to get from somewhere else :

```
add_filter('st_cf_mail_from',function() {
    return 'Your Mom <yourmom@gmail.com>';
});
```

### Manipulate the subject of the email

If you want to change the subject of emails :

```
add_filter('st_cf_mail_subject',function($subject) {
    return '[My rebranded CMS] '.$subject;
});
```

### Manipulate the content of the email

If you want to add templating to your email :

```
add_filter('st_cf_mail_content',function($content) {
    return '<html><body style="background:pink;">'.$content.'</body></html>';
});
```


## HEADERS

Default mail headers are :

```
'From: '.$from
'Content-Type: text/html; charset=UTF-8'
```

If you have a "Courriel" field a replyTo will be added to your headers.

### Field name can be changed
```
add_filter('st_cf_mail_field',function($field) {
    return 'Email';
});
```

### Modify mail headers array
```
add_filter('st_cf_mail_headers',function($headers) {
    $headers[] = "X-Some: More-Headers";
    return $headers;
});
```

### Modify basic message on top of email
```
add_filter('st_cf_mailmsg',function($msg) {
    return 'New form entry : ';
    // Default : Nouveau formulaire reçu, voici l'information
});
```


## HEADERS

Default mail headers are :

```
'From: '.$from
'Content-Type: text/html; charset=UTF-8'
```

If you have a "Courriel" field a replyTo will be added to your headers.

### Field name can be changed
```
add_filter('st_cf_mail_field',function($field) {
    return 'Email';
});
```

### Modify mail headers array
```
add_filter('st_cf_mail_headers',function($headers) {
    $headers[] = "X-Some: More-Headers";
    return $headers;
});
```
### Add callback function
```
<form class="[any other classes] js-stereo-cf" data-callback="functionName">
    <input name="Prenom" required>
    <input name="Nom" required>
    <input type="submit">
</form>
<div style="display:none">Thanks.</div>
```
