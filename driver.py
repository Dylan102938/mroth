import scraper
import send_results
import time
import csv


def main():
    while True:
        time.sleep(300)
        scraper.main()

        try:
            file = open("output.csv")
            reader = csv.reader(file)

            if len(list(reader)) > 1:
                send_results.main()
                print("Sent results")
            else:
                print("No results to send")

        except:
            pass


if __name__ == "__main__":
    main()
