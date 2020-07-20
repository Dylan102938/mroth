import smtplib
from email.mime.multipart import MIMEMultipart
from email.mime.text import MIMEText
from email.mime.base import MIMEBase
from email import encoders

fromaddr = "deposits@templar.fund"
toaddr = "mgt51@tutanota.com"

msg = MIMEMultipart()

msg['From'] = fromaddr
msg['To'] = toaddr
msg['Subject'] = "Results of BitCoin Scraper"
body = "Below are the Accounts.CSV, Output.CSV, and Latest.CSV files of the run."
msg.attach(MIMEText(body, 'plain'))

accounts = open("accounts.csv", "rb")
p = MIMEBase('application', 'octet-stream')
p.set_payload((accounts).read())
encoders.encode_base64(p)
p.add_header('Content-Disposition', "attachment; filename= accounts.csv")

latest = open("latest.csv", "rb")
p1 = MIMEBase('application', 'octet-stream')
p1.set_payload((latest).read())
encoders.encode_base64(p1)
p1.add_header('Content-Disposition', "attachment; filename= latest.csv")

output = open("output.csv", "rb")
p2 = MIMEBase('application', 'octet-stream')
p2.set_payload((output).read())
encoders.encode_base64(p2)
p2.add_header('Content-Disposition', "attachment; filename= output.csv")

msg.attach(p)
msg.attach(p1)
msg.attach(p2)

s = smtplib.SMTP('mail.templar.fund', 587)
s.starttls()
s.login(fromaddr, "Dylan2020")
text = msg.as_string()
s.sendmail(fromaddr, toaddr, text)
s.quit()