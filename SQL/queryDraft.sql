USE DBMSAssignmentOrderBook
GO

-- DECLARE @account_id INT

-- SELECT @account_id = Account_ID
-- FROM ACCOUNT
-- WHERE Account_ID = 2

-- INSERT INTO CUSTOMER
--     (Customer_ID)
-- VALUES
--     (@account_ID)



-- SELECT BOOK.Book_name, BOOK.Book_ID, BELONGS_TO.Genre_ID, GENRE.Genre_name, WRITE.Author_ID, AUTHOR.Author_name
-- FROM BOOK
--     INNER JOIN
--     BELONGS_TO ON BELONGS_TO.Book_ID=BOOK.Book_ID
--     INNER JOIN
--     WRITE ON WRITE.Book_ID = Book.Book_ID
--     INNER JOIN
--     GENRE ON GENRE.Genre_ID = BELONGS_TO.Genre_ID
--     INNER JOIN
--     AUTHOR ON WRITE.Author_ID= AUTHOR.Author_ID


-- DELETE FROM PUBLISH
-- DELETE FROM BELONGS_TO
-- DELETE FROM WRITE
-- DELETE FROM BOOK

-- SELECT BOOK.Book_ID, BOOK.Book_name, PUBLISHER.Publisher_ID, PUBLISHER.Publisher_name
-- FROM BOOK, PUBLISHER, PUBLISH
-- WHERE BOOK.Book_ID = PUBLISH.Book_ID AND PUBLISH.Publisher_ID = PUBLISHER.Publisher_I

SELECT COUNT(*)
AS 'RowCount'
FROM BOOK