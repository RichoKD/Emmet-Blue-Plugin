CREATE SCHEMA Patients;
GO

CREATE TABLE Patients.Patient (
	PatientID INT PRIMARY KEY IDENTITY,
	PatientFirstName VARCHAR(20),
	PatientLastName VARCHAR(20),
	PatientDateOfBirth DATE,
	PatientAddress VARCHAR(100),
	PatientPhoneNumber VARCHAR(20),
	PatientUUID INT UNIQUE NOT NULL,
)
GO

CREATE TABLE Patients.PatientDepartment (
	PatientDepartmentID INT PRIMARY KEY IDENTITY NOT NULL,
	PatientID INT,
	DepartmentID INT,
	FOREIGN KEY (PatientID) REFERENCES Patients.Patient(PatientID) ON UPDATE CASCADE ON DELETE CASCADE
)
GO

CREATE TABLE Patients.PatientTransaction(
	PatientTransactionID INT PRIMARY KEY IDENTITY NOT NULL,
	PatientID INT,
	Link VARCHAR(max),
	Meta VARCHAR(max),
	FOREIGN KEY (PatientID) REFERENCES Patients.Patient(PatientID) ON UPDATE CASCADE ON DELETE CASCADE
)
GO

CREATE TABLE Patients.PatientRepository (
	RepositoryItemID INT PRIMARY KEY IDENTITY NOT NULL,
	PatientID INT,
	RepositoryItemNumber VARCHAR(50) NOT NULL UNIQUE,
	RepositoryItemName VARCHAR(100),
	RepositoryItemDescription VARCHAR(4000),
	RepositoryItemUrl VARCHAR(MAX),
	FOREIGN KEY (PatientID) REFERENCES Patients.Patient(PatientID) ON UPDATE CASCADE ON DELETE CASCADE
)
GO