def get_Item_Count(client, TableName):
    TableName = TableName
    c = client
    ItemCount = c.scan(
        TableName=TableName
        )
    ItemCount = ItemCount["Count"]
    return ItemCount