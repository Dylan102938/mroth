import scraper
import send_results
import time
import csv


def main():
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
