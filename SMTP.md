# /includes/library/Net/SMTP.php #
  * 作者:Chris Ryan
  * 版权:Chris Ryan
  * 协议:LGPL
  * 版本:1.02

## 类:SMTP ##
  * 描述 - `SMTP - PHP SMTP class`

### public `SMTP`() ###
  * 返回: **void**
  * 位置: 文件 **/includes/library/Net/SMTP.php** 第 **52** 行
  * 说明: Initialize the class so that the data is in a known state.

### public `Connect`() ###
  * 返回: **bool**
  * 位置: 文件 **/includes/library/Net/SMTP.php** 第 **77** 行
  * 说明: Connect to the server specified on the port specified.If the port is not specified use the default SMTP\_PORT.If tval is specified then a connection will try and beestablished with the server for that number of seconds.If tval is not specified the default is 30 seconds totry on the connection.SMTP CODE SUCCESS: 220SMTP CODE FAILURE: 421

### public `Authenticate`() ###
  * 返回: **bool**
  * 位置: 文件 **/includes/library/Net/SMTP.php** 第 **139** 行
  * 说明: Performs SMTP authentication.  Must be run after running theHello() method.  Returns true if successfully authenticated.

### private `Connected`() ###
  * 返回: **bool**
  * 位置: 文件 **/includes/library/Net/SMTP.php** 第 **202** 行
  * 说明: Returns true if connected to a server otherwise false

### public `Close`() ###
  * 返回: **void**
  * 位置: 文件 **/includes/library/Net/SMTP.php** 第 **227** 行
  * 说明: Closes the socket and cleans up the state of the class.It is not considered good to use this function withoutfirst trying to use QUIT.

### public `Data`() ###
  * 返回: **bool**
  * 位置: 文件 **/includes/library/Net/SMTP.php** 第 **261** 行
  * 说明: Issues a data command and sends the msg\_data to the serverfinializing the mail transaction. $msg\_data is the messagethat is to be send with the headers. Each header needs to beon a single line followed by a 

&lt;CRLF&gt;

 with the message headersand the message body being seperated by and additional 

&lt;CRLF&gt;

.Implements rfc 821: DATA 

&lt;CRLF&gt;

SMTP CODE INTERMEDIATE: 354    [data](data.md)    

&lt;CRLF&gt;

.

&lt;CRLF&gt;

    SMTP CODE SUCCESS: 250    SMTP CODE FAILURE: 552,554,451,452SMTP CODE FAILURE: 451,554SMTP CODE ERROR  : 500,501,503,421

### public `Expand`() ###
  * 返回: **string**
  * 位置: 文件 **/includes/library/Net/SMTP.php** 第 **400** 行
  * 说明: Expand takes the name and asks the server to list all thepeople who are members of the _list_. Expand will returnback and array of the result or false if an error occurs.Each value in the array returned has the format of:    [&lt;full-name&gt; &lt;sp&gt; ](.md) 

&lt;path&gt;

The definition of 

&lt;path&gt;

 is defined in rfc 821Implements rfc 821: EXPN 

&lt;SP&gt;

 

&lt;string&gt;

 

&lt;CRLF&gt;

SMTP CODE SUCCESS: 250SMTP CODE FAILURE: 550SMTP CODE ERROR  : 500,501,502,504,421

### public `Hello`() ###
  * 返回: **bool**
  * 位置: 文件 **/includes/library/Net/SMTP.php** 第 **451** 行
  * 说明: Sends the HELO command to the smtp server.This makes sure that we and the server are inthe same known state.Implements from rfc 821: HELO 

&lt;SP&gt;

 

&lt;domain&gt;

 

&lt;CRLF&gt;

SMTP CODE SUCCESS: 250SMTP CODE ERROR  : 500, 501, 504, 421

### private `SendHello`() ###
  * 返回: **bool**
  * 位置: 文件 **/includes/library/Net/SMTP.php** 第 **483** 行
  * 说明: Sends a HELO/EHLO command.

### public `Help`() ###
  * 返回: **string**
  * 位置: 文件 **/includes/library/Net/SMTP.php** 第 **525** 行
  * 说明: Gets help information on the keyword specified. If the keywordis not specified then returns generic help, ussually contianingA list of keywords that help is available on. This functionreturns the results back to the user. It is up to the user tohandle the returned data. If an error occurs then false isreturned with $this->error set appropiately.Implements rfc 821: HELP [&lt;SP&gt; &lt;string&gt; ](.md) 

&lt;CRLF&gt;

SMTP CODE SUCCESS: 211,214SMTP CODE ERROR  : 500,501,502,504,421

### public `Mail`() ###
  * 返回: **bool**
  * 位置: 文件 **/includes/library/Net/SMTP.php** 第 **577** 行
  * 说明: Starts a mail transaction from the email address specified in$from. Returns true if successful or false otherwise. If Truethe mail transaction is started and then one or more Recipientcommands may be called followed by a Data command.Implements rfc 821: MAIL 

&lt;SP&gt;

 FROM:

&lt;reverse-path&gt;

 

&lt;CRLF&gt;

SMTP CODE SUCCESS: 250SMTP CODE SUCCESS: 552,451,452SMTP CODE SUCCESS: 500,501,421

### public `Noop`() ###
  * 返回: **bool**
  * 位置: 文件 **/includes/library/Net/SMTP.php** 第 **619** 行
  * 说明: Sends the command NOOP to the SMTP server.Implements from rfc 821: NOOP 

&lt;CRLF&gt;

SMTP CODE SUCCESS: 250SMTP CODE ERROR  : 500, 421

### public `Quit`() ###
  * 返回: **bool**
  * 位置: 文件 **/includes/library/Net/SMTP.php** 第 **662** 行
  * 说明: Sends the quit command to the server and then closes the socketif there is no error or the $close\_on\_error argument is true.Implements from rfc 821: QUIT 

&lt;CRLF&gt;

SMTP CODE SUCCESS: 221SMTP CODE ERROR  : 500

### public `Recipient`() ###
  * 返回: **bool**
  * 位置: 文件 **/includes/library/Net/SMTP.php** 第 **716** 行
  * 说明: Sends the command RCPT to the SMTP server with the TO: argument of $to.Returns true if the recipient was accepted false if it was rejected.Implements from rfc 821: RCPT 

&lt;SP&gt;

 TO:

&lt;forward-path&gt;

 

&lt;CRLF&gt;

SMTP CODE SUCCESS: 250,251SMTP CODE FAILURE: 550,551,552,553,450,451,452SMTP CODE ERROR  : 500,501,503,421

### public `Reset`() ###
  * 返回: **bool**
  * 位置: 文件 **/includes/library/Net/SMTP.php** 第 **760** 行
  * 说明: Sends the RSET command to abort and transaction that iscurrently in progress. Returns true if successful falseotherwise.Implements rfc 821: RSET 

&lt;CRLF&gt;

SMTP CODE SUCCESS: 250SMTP CODE ERROR  : 500,501,504,421

### public `Send`() ###
  * 返回: **bool**
  * 位置: 文件 **/includes/library/Net/SMTP.php** 第 **809** 行
  * 说明: Starts a mail transaction from the email address specified in$from. Returns true if successful or false otherwise. If Truethe mail transaction is started and then one or more Recipientcommands may be called followed by a Data command. This commandwill send the message to the users terminal if they are loggedin.Implements rfc 821: SEND 

&lt;SP&gt;

 FROM:

&lt;reverse-path&gt;

 

&lt;CRLF&gt;

SMTP CODE SUCCESS: 250SMTP CODE SUCCESS: 552,451,452SMTP CODE SUCCESS: 500,501,502,421

### public `SendAndMail`() ###
  * 返回: **bool**
  * 位置: 文件 **/includes/library/Net/SMTP.php** 第 **857** 行
  * 说明: Starts a mail transaction from the email address specified in$from. Returns true if successful or false otherwise. If Truethe mail transaction is started and then one or more Recipientcommands may be called followed by a Data command. This commandwill send the message to the users terminal if they are loggedin and send them an email.Implements rfc 821: SAML 

&lt;SP&gt;

 FROM:

&lt;reverse-path&gt;

 

&lt;CRLF&gt;

SMTP CODE SUCCESS: 250SMTP CODE SUCCESS: 552,451,452SMTP CODE SUCCESS: 500,501,502,421

### public `SendOrMail`() ###
  * 返回: **bool**
  * 位置: 文件 **/includes/library/Net/SMTP.php** 第 **905** 行
  * 说明: Starts a mail transaction from the email address specified in$from. Returns true if successful or false otherwise. If Truethe mail transaction is started and then one or more Recipientcommands may be called followed by a Data command. This commandwill send the message to the users terminal if they are loggedin or mail it to them if they are not.Implements rfc 821: SOML 

&lt;SP&gt;

 FROM:

&lt;reverse-path&gt;

 

&lt;CRLF&gt;

SMTP CODE SUCCESS: 250SMTP CODE SUCCESS: 552,451,452SMTP CODE SUCCESS: 500,501,502,421

### public `Turn`() ###
  * 返回: **bool**
  * 位置: 文件 **/includes/library/Net/SMTP.php** 第 **950** 行
  * 说明: This is an optional command for SMTP that this class does notsupport. This method is here to make the RFC821 Definitioncomplete for this class and may be implimented in the futureImplements from rfc 821: TURN 

&lt;CRLF&gt;

SMTP CODE SUCCESS: 250SMTP CODE FAILURE: 502SMTP CODE ERROR  : 500, 503

### public `Verify`() ###
  * 返回: **int**
  * 位置: 文件 **/includes/library/Net/SMTP.php** 第 **972** 行
  * 说明: Verifies that the name is recognized by the server.Returns false if the name could not be verified otherwisethe response from the server is returned.Implements rfc 821: VRFY 

&lt;SP&gt;

 

&lt;string&gt;

 

&lt;CRLF&gt;

SMTP CODE SUCCESS: 250,251SMTP CODE FAILURE: 550,551,553SMTP CODE ERROR  : 500,501,502,421

### private `get_lines`() ###
  * 返回: **string**
  * 位置: 文件 **/includes/library/Net/SMTP.php** 第 **1017** 行
  * 说明: Read in as many lines as possibleeither before eof or socket timeout occurs on the operation.With SMTP we can tell if we have more lines to read if the4th character is '-' symbol. If it is a space then we don'tneed to read anything else.


## 类:Phpmailer ##
  * 描述 - `PHPMailer - PHP email transport class`
  * 包 - `PHPMailer`

### `IsHTML`(<sup>bool</sup> `$bool`) ###
**参数列表**
|**名称**|**类型**|**默认**|**说明**|
|:---------|:---------|:---------|:---------|
|$bool     |bool      |_N/A_     |          |

  * 返回: **void**
  * 位置: 文件 **/includes/library/Net/SMTP.php** 第 **1273** 行
  * 说明: Sets message type to HTML.

### `IsSSL`(<sup>bool</sup> `$bool`) ###
**参数列表**
|**名称**|**类型**|**默认**|**说明**|
|:---------|:---------|:---------|:---------|
|$bool     |bool      |_N/A_     |          |

  * 返回: **void**
  * 位置: 文件 **/includes/library/Net/SMTP.php** 第 **1285** 行
  * 说明: Sets message type to HTML.

### `IsSMTP`() ###
  * 返回: **void**
  * 位置: 文件 **/includes/library/Net/SMTP.php** 第 **1293** 行
  * 说明: Sets Mailer to send message using SMTP.

### `IsMail`() ###
  * 返回: **void**
  * 位置: 文件 **/includes/library/Net/SMTP.php** 第 **1301** 行
  * 说明: Sets Mailer to send message using PHP mail() function.

### `IsSendmail`() ###
  * 返回: **void**
  * 位置: 文件 **/includes/library/Net/SMTP.php** 第 **1309** 行
  * 说明: Sets Mailer to send message using the $Sendmail program.

### `IsQmail`() ###
  * 返回: **void**
  * 位置: 文件 **/includes/library/Net/SMTP.php** 第 **1317** 行
  * 说明: Sets Mailer to send message using the qmail MTA.

### `AddAddress`(<sup>string</sup> `$address`, <sup>string</sup> `$name`) ###
**参数列表**
|**名称**|**类型**|**默认**|**说明**|
|:---------|:---------|:---------|:---------|
|$address  |string    |_N/A_     |          |
|$name     |string    |""        |          |

  * 返回: **void**
  * 位置: 文件 **/includes/library/Net/SMTP.php** 第 **1333** 行
  * 说明: Adds a "To" address.

### `AddCC`(<sup>string</sup> `$address`, <sup>string</sup> `$name`) ###
**参数列表**
|**名称**|**类型**|**默认**|**说明**|
|:---------|:---------|:---------|:---------|
|$address  |string    |_N/A_     |          |
|$name     |string    |""        |          |

  * 返回: **void**
  * 位置: 文件 **/includes/library/Net/SMTP.php** 第 **1347** 行
  * 说明: Adds a "Cc" address. Note: this function workswith the SMTP mailer on win32, not with the "mail"mailer.

### `AddBCC`(<sup>string</sup> `$address`, <sup>string</sup> `$name`) ###
**参数列表**
|**名称**|**类型**|**默认**|**说明**|
|:---------|:---------|:---------|:---------|
|$address  |string    |_N/A_     |          |
|$name     |string    |""        |          |

  * 返回: **void**
  * 位置: 文件 **/includes/library/Net/SMTP.php** 第 **1361** 行
  * 说明: Adds a "Bcc" address. Note: this function workswith the SMTP mailer on win32, not with the "mail"mailer.

### `AddReplyTo`(<sup>string</sup> `$address`, <sup>string</sup> `$name`) ###
**参数列表**
|**名称**|**类型**|**默认**|**说明**|
|:---------|:---------|:---------|:---------|
|$address  |string    |_N/A_     |          |
|$name     |string    |""        |          |

  * 返回: **void**
  * 位置: 文件 **/includes/library/Net/SMTP.php** 第 **1373** 行
  * 说明: Adds a "Reply-to" address.

### `Send`() ###
  * 返回: **bool**
  * 位置: 文件 **/includes/library/Net/SMTP.php** 第 **1390** 行
  * 说明: Creates message and assigns Mailer. If the message isnot sent successfully then it returns false.  Use the ErrorInfovariable to view description of the error.

### private `SendmailSend`() ###
  * 返回: **bool**
  * 位置: 文件 **/includes/library/Net/SMTP.php** 第 **1438** 行
  * 说明: Sends mail using the $Sendmail program.

### private `MailSend`() ###
  * 返回: **bool**
  * 位置: 文件 **/includes/library/Net/SMTP.php** 第 **1468** 行
  * 说明: Sends mail using the PHP mail() function.

### private `SmtpSend`() ###
  * 返回: **bool**
  * 位置: 文件 **/includes/library/Net/SMTP.php** 第 **1506** 行
  * 说明: Sends mail via SMTP using PhpSMTP (Author:Chris Ryan).  Returns bool.  Returns false if there is abad MAIL FROM, RCPT, or DATA input.

### private `SmtpConnect`() ###
  * 返回: **bool**
  * 位置: 文件 **/includes/library/Net/SMTP.php** 第 **1572** 行
  * 说明: Initiates a connection to an SMTP server.  Returns false if theoperation failed.

### `SmtpClose`() ###
  * 返回: **void**
  * 位置: 文件 **/includes/library/Net/SMTP.php** 第 **1628** 行
  * 说明: Closes the active SMTP session if one exists.

### private `AddrAppend`() ###
  * 返回: **string**
  * 位置: 文件 **/includes/library/Net/SMTP.php** 第 **1648** 行
  * 说明: Creates recipient headers.

### private `AddrFormat`() ###
  * 返回: **string**
  * 位置: 文件 **/includes/library/Net/SMTP.php** 第 **1666** 行
  * 说明: Formats an address correctly.

### private `WrapText`() ###
  * 返回: **string**
  * 位置: 文件 **/includes/library/Net/SMTP.php** 第 **1685** 行
  * 说明: Wraps message for use with mailers that do notautomatically perform wrapping and for quoted-printable.Original written by philippe.

### private `SetWordWrap`() ###
  * 返回: **void**
  * 位置: 文件 **/includes/library/Net/SMTP.php** 第 **1763** 行
  * 说明: Set the body wrapping.

### private `CreateHeader`() ###
  * 返回: **string**
  * 位置: 文件 **/includes/library/Net/SMTP.php** 第 **1785** 行
  * 说明: Assembles message header.

### private `CreateBody`() ###
  * 返回: **string**
  * 位置: 文件 **/includes/library/Net/SMTP.php** 第 **1883** 行
  * 说明: Assembles the message body.  Returns an empty string on failure.

### private `GetBoundary`() ###
  * 位置: 文件 **/includes/library/Net/SMTP.php** 第 **1949** 行
  * 说明: Returns the start of a message boundary.

### private `EndBoundary`() ###
  * 位置: 文件 **/includes/library/Net/SMTP.php** 第 **1969** 行
  * 说明: Returns the end of a message boundary.

### private `SetMessageType`() ###
  * 返回: **void**
  * 位置: 文件 **/includes/library/Net/SMTP.php** 第 **1978** 行
  * 说明: Sets the message type.

### private `HeaderLine`() ###
  * 返回: **string**
  * 位置: 文件 **/includes/library/Net/SMTP.php** 第 **1997** 行
  * 说明: Returns a formatted header line.

### private `TextLine`() ###
  * 返回: **string**
  * 位置: 文件 **/includes/library/Net/SMTP.php** 第 **2006** 行
  * 说明: Returns a formatted mail line.

### `AddAttachment`(<sup>string</sup> `$path`, <sup>string</sup> `$name`, <sup>string</sup> `$encoding`, <sup>string</sup> `$type`) ###
**参数列表**
|**名称**|**类型**|**默认**|**说明**|
|:---------|:---------|:---------|:---------|
|$path     |string    |_N/A_     |Path      |
|$name     |string    |""        |Overrides |
|$encoding |string    |"base64"  |File      |
|$type     |string    |"application/octet-stream"|File      |

  * 返回: **bool**
  * 位置: 文件 **/includes/library/Net/SMTP.php** 第 **2024** 行
  * 说明: Adds an attachment from a path on the filesystem.Returns false if the file could not be foundor accessed.

### private `AttachAll`() ###
  * 返回: **string**
  * 位置: 文件 **/includes/library/Net/SMTP.php** 第 **2055** 行
  * 说明: Attaches all fs, string, and binary attachments to the message.Returns an empty string on failure.

### private `EncodeFile`() ###
  * 返回: **string**
  * 位置: 文件 **/includes/library/Net/SMTP.php** 第 **2112** 行
  * 说明: Encodes attachment in requested format.  Returns anempty string on failure.

### private `EncodeString`() ###
  * 返回: **string**
  * 位置: 文件 **/includes/library/Net/SMTP.php** 第 **2134** 行
  * 说明: Encodes string to requested format. Returns anempty string on failure.

### private `EncodeHeader`() ###
  * 返回: **string**
  * 位置: 文件 **/includes/library/Net/SMTP.php** 第 **2165** 行
  * 说明: Encode a header string to best of Q, B, quoted or none.

### private `EncodeQP`() ###
  * 返回: **string**
  * 位置: 文件 **/includes/library/Net/SMTP.php** 第 **2218** 行
  * 说明: Encode string to quoted-printable.

### private `EncodeQ`() ###
  * 返回: **string**
  * 位置: 文件 **/includes/library/Net/SMTP.php** 第 **2241** 行
  * 说明: Encode string to q encoding.

### `AddStringAttachment`(<sup>string</sup> `$string`, <sup>string</sup> `$filename`, <sup>string</sup> `$encoding`, <sup>string</sup> `$type`) ###
**参数列表**
|**名称**|**类型**|**默认**|**说明**|
|:---------|:---------|:---------|:---------|
|$string   |string    |_N/A_     |String    |
|$filename |string    |_N/A_     |Name      |
|$encoding |string    |"base64"  |File      |
|$type     |string    |"application/octet-stream"|File      |

  * 返回: **void**
  * 位置: 文件 **/includes/library/Net/SMTP.php** 第 **2275** 行
  * 说明: Adds a string or binary attachment (non-filesystem) to the list.This method can be used to attach ascii or binary data,such as a BLOB record from a database.

### `AddEmbeddedImage`(<sup>string</sup> `$path`, <sup>string</sup> `$cid`, <sup>string</sup> `$name`, <sup>string</sup> `$encoding`, <sup>string</sup> `$type`) ###
**参数列表**
|**名称**|**类型**|**默认**|**说明**|
|:---------|:---------|:---------|:---------|
|$path     |string    |_N/A_     |Path      |
|$cid      |string    |_N/A_     |Content   |
|$name     |string    |""        |Overrides |
|$encoding |string    |"base64"  |File      |
|$type     |string    |"application/octet-stream"|File      |

  * 返回: **bool**
  * 位置: 文件 **/includes/library/Net/SMTP.php** 第 **2302** 行
  * 说明: Adds an embedded attachment.  This can include images, sounds, andjust about any other document.  Make sure to set the $type to animage type.  For JPEG images use "image/jpeg" and for GIF imagesuse "image/gif".       the Id for accessing the image in an HTML form.

### private `InlineImageExists`() ###
  * 返回: **bool**
  * 位置: 文件 **/includes/library/Net/SMTP.php** 第 **2334** 行
  * 说明: Returns true if an inline attachment is present.

### `ClearAddresses`() ###
  * 返回: **void**
  * 位置: 文件 **/includes/library/Net/SMTP.php** 第 **2356** 行
  * 说明: Clears all recipients assigned in the TO array.  Returns void.

### `ClearCCs`() ###
  * 返回: **void**
  * 位置: 文件 **/includes/library/Net/SMTP.php** 第 **2364** 行
  * 说明: Clears all recipients assigned in the CC array.  Returns void.

### `ClearBCCs`() ###
  * 返回: **void**
  * 位置: 文件 **/includes/library/Net/SMTP.php** 第 **2372** 行
  * 说明: Clears all recipients assigned in the BCC array.  Returns void.

### `ClearReplyTos`() ###
  * 返回: **void**
  * 位置: 文件 **/includes/library/Net/SMTP.php** 第 **2380** 行
  * 说明: Clears all recipients assigned in the ReplyTo array.  Returns void.

### `ClearAllRecipients`() ###
  * 返回: **void**
  * 位置: 文件 **/includes/library/Net/SMTP.php** 第 **2389** 行
  * 说明: Clears all recipients assigned in the TO, CC and BCCarray.  Returns void.

### `ClearAttachments`() ###
  * 返回: **void**
  * 位置: 文件 **/includes/library/Net/SMTP.php** 第 **2400** 行
  * 说明: Clears all previously set filesystem, string, and binaryattachments.  Returns void.

### `ClearCustomHeaders`() ###
  * 返回: **void**
  * 位置: 文件 **/includes/library/Net/SMTP.php** 第 **2408** 行
  * 说明: Clears all custom headers.  Returns void.

### private `SetError`() ###
  * 返回: **void**
  * 位置: 文件 **/includes/library/Net/SMTP.php** 第 **2423** 行
  * 说明: Adds the error message to the error container.Returns void.

### private `RFCDate`() ###
  * 返回: **string**
  * 位置: 文件 **/includes/library/Net/SMTP.php** 第 **2433** 行
  * 说明: Returns the proper RFC 822 formatted date.

### private `ServerVar`() ###
  * 返回: **mixed**
  * 位置: 文件 **/includes/library/Net/SMTP.php** 第 **2450** 行
  * 说明: Returns the appropriate server variable.  Should work with bothPHP 4.1.0+ as well as older versions.  Returns an empty stringif nothing is found.

### private `ServerHostname`() ###
  * 返回: **string**
  * 位置: 文件 **/includes/library/Net/SMTP.php** 第 **2472** 行
  * 说明: Returns the server hostname or 'localhost.localdomain' if unknown.

### `IsError`() ###
  * 返回: **bool**
  * 位置: 文件 **/includes/library/Net/SMTP.php** 第 **2487** 行
  * 说明: Returns true if an error occurred.

### private `FixEOL`() ###
  * 返回: **string**
  * 位置: 文件 **/includes/library/Net/SMTP.php** 第 **2496** 行
  * 说明: Changes every end of line from CR or LF to CRLF.

### `AddCustomHeader`() ###
  * 返回: **void**
  * 位置: 文件 **/includes/library/Net/SMTP.php** 第 **2507** 行
  * 说明: Adds a custom header.