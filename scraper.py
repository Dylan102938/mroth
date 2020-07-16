import requests
import csv
import json


def main():
    accounts = []
    timestamps = []
    proxies = []
    with open("accounts.csv", mode="r") as infile:
        accounts = list(csv.reader(infile))

    accounts = accounts[1:]

    with open("latest.csv", mode="r") as prevfile:
        timestamps = list(csv.reader(prevfile))
    new_timestamps = timestamps

    s = requests.session()
    result = s.get("https://blockchain.info/ticker?base=BTC")
    result = json.loads(result.text)
    curr_btc_val = result["USD"]["last"]

    output = [["ClientID", "SubAccount", "BTCAddress", "TransactionAmount", "DiscountedAmount", "NetTransactionValue",
               "TransactionTime"]]

    for i, account in enumerate(accounts):
        if i % 9 == 0:
            s = requests.session()

        btc_address = account[3]
        latest = find_latest_time(btc_address, timestamps)
        client_id = account[0]
        sub_account = account[1]
        discount_rate = account[2]

        print("Currently Scraping Client ID:", client_id, "Sub Account:", sub_account)

        result = s.get("https://blockchain.info/address/" + btc_address + "?format=json&offset=0")
        transactions = json.loads(result.text)
        print(transactions)

        latest_time = 0
        for transaction in transactions["txs"]:
            total_blocks = s.get("https://blockchain.info/q/getblockcount")
            total_blocks = int(total_blocks.text)

            transaction_height = s.get("https://blockchain.info/tx/" + transaction["hash"] + "?show_adv=false&format=json")

            try:
                transaction_height = json.loads(transaction_height.text)["block_height"]
            except:
                transaction_height = total_blocks

            if transaction_height > latest and transaction["result"] > 0:
                if (total_blocks - transaction_height + 1) >= 6:
                    print("Number of confirmations:", (total_blocks - transaction_height + 1))
                    usd = float(transaction["result"])/float(100000000)*curr_btc_val
                    discount = float(discount_rate[:-1])/100*usd
                    net = usd - discount

                    output.append([client_id, sub_account, btc_address, usd, discount, net, transaction_height])

                    if transaction_height > latest_time:
                        latest_time = transaction_height
                        new_timestamps = update_latest_time(btc_address, new_timestamps, latest_time)

    with open("output.csv", 'w') as csvfile:
        csvwriter = csv.writer(csvfile)
        csvwriter.writerows(output)

    with open("latest.csv", 'w') as csvfile:
        csvwriter = csv.writer(csvfile)
        csvwriter.writerows(new_timestamps)


def find_latest_time(address, transactions):
    latest_time = 0
    for transaction in transactions:
        try:
            if transaction[0] == address:
                latest_time = float(transaction[1])
                return latest_time
        except:
            pass

    return latest_time


def update_latest_time(address, transactions, latest_time):
    for i, transaction in enumerate(transactions):
        try:
            if transaction[0] == address:
                transactions[i][1] = latest_time
                return transactions
        except:
            pass

    transactions.append([address, latest_time])
    return transactions


if __name__ == "__main__":
    main()