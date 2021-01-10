import json
import boto3

def get_Existing_Data(client, TableName):
    TableName = TableName
    c = client
    r = c.scan(
        TableName=TableName
        )
    ExistingData = r["Items"]
    return ExistingData