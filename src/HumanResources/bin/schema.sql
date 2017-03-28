CREATE SCHEMA Staffs
GO

CREATE TABLE [Staffs].[DepartmentGroup] (
	DepartmentGroupID INT PRIMARY KEY IDENTITY,
	GroupName VARCHAR(50) UNIQUE,
	ModifiedDate DATETIME
)

CREATE TABLE [Staffs].[Department] (
	DepartmentID INT PRIMARY KEY IDENTITY,
	Name VARCHAR(50),
	GroupID INT,
	ModifiedDate DATETIME DEFAULT GETDATE(),
	FOREIGN KEY (GroupID) REFERENCES [Staffs].[DepartmentGroup] (DepartmentGroupID) ON UPDATE CASCADE ON DELETE CASCADE
)

CREATE TABLE Staffs.DepartmentRootUrl (
	UrlID INT PRIMARY KEY IDENTITY,
	DepartmentID INT UNIQUE,
	Url VARCHAR(200) NOT NULL,
	FOREIGN KEY (DepartmentID) REFERENCES [Staffs].[Department] ON UPDATE CASCADE ON DELETE CASCADE
)

CREATE TABLE [Staffs].[Role](
	RoleID INT PRIMARY KEY IDENTITY,
	Name VARCHAR(50) NOT NULL,
	DepartmentID INT NOT NULL,
	Description VARCHAR(200),
	ModifiedDate DATETIME DEFAULT GETDATE(),
	FOREIGN KEY (DepartmentID) REFERENCES [Staffs].[Department] ON UPDATE CASCADE ON DELETE CASCADE
)

CREATE TABLE [Staffs].[Staff] (
	StaffID INT PRIMARY KEY IDENTITY,
	StaffUUID VARCHAR(20) UNIQUE,
	AccountActivated BIT DEFAULT 0 NOT NULL,
	ModifiedDate DATETIME
)

CREATE TABLE [Staffs].[StaffPassword] (
	StaffPasswordID INT PRIMARY KEY IDENTITY,
	StaffID INT UNIQUE,
	StaffUsername VARCHAR(20) UNIQUE,
	PasswordHash VARCHAR(MAX),
	PasswordSalt VARCHAR(20),
	LoggedIn BIT NOT NULL DEFAULT 0,
	ModifiedDate DATETIME DEFAULT GETDATE(),
	FOREIGN KEY (StaffID) REFERENCES [Staffs].[Staff] (StaffID) ON UPDATE CASCADE ON DELETE CASCADE
)

CREATE TABLE [Staffs].[StaffDepartment] (
	StaffDepartmentID INT PRIMARY KEY IDENTITY,
	StaffID INT UNIQUE,
	DepartmentID INT,
	ModifiedDate DATETIME DEFAULT GETDATE(),
	FOREIGN KEY (DepartmentID) REFERENCES [Staffs].[Department] ON UPDATE CASCADE ON DELETE SET NULL,
	FOREIGN KEY (StaffID) REFERENCES [Staffs].[Staff] (StaffID) ON UPDATE CASCADE ON DELETE CASCADE
)

CREATE TABLE [Staffs].[StaffRole] (
	StaffRoleID INT PRIMARY KEY IDENTITY,
	StaffID INT UNIQUE,
	RoleID INT,
	ModifiedDate DATETIME DEFAULT GETDATE(),
	FOREIGN KEY (RoleID) REFERENCES [Staffs].[Role] ON UPDATE CASCADE ON DELETE SET NULL,
	FOREIGN KEY (StaffID) REFERENCES [Staffs].[Staff] (StaffID) ON UPDATE CASCADE ON DELETE CASCADE
)

CREATE TABLE Staffs.StaffProfile (
	StaffProfile INT PRIMARY KEY IDENTITY,
	StaffID INT NOT NULL,
	StaffFullName VARCHAR(50),
	StaffPicture VARCHAR(MAX),
	StaffIdentificationDocument VARCHAR(MAX),
	LastModified DATETIME DEFAULT GETDATE()
)

CREATE TABLE Staffs.StaffRecordsFieldTitle (
	FieldTitleID INT PRIMARY KEY IDENTITY NOT NULL,
	FieldTitleName VARCHAR(50) UNIQUE,
	FieldTitleType VARCHAR(50),
	FieldTitleDescription VARCHAR(50)
)

CREATE TABLE Staffs.StaffRecordsFieldValue (
	FieldValueID INT PRIMARY KEY IDENTITY NOT NULL,
	StaffID INT,
	FieldTitle VARCHAR(50),
	FieldValue VARCHAR(max),
	FOREIGN KEY (StaffID) REFERENCES Staffs.Staff(StaffID) ON UPDATE CASCADE ON DELETE CASCADE
)

CREATE TABLE Staffs.StaffSecondaryDepartments (
	StaffDepartmentID INT PRIMARY KEY IDENTITY,
	StaffID INT,
	DepartmentID INT,
	ModifiedDate DATETIME DEFAULT GETDATE(),
	UNIQUE(StaffID, DepartmentID),
	FOREIGN KEY (DepartmentID) REFERENCES [Staffs].[Department] ON UPDATE CASCADE ON DELETE SET NULL,
	FOREIGN KEY (StaffID) REFERENCES [Staffs].[Staff] (StaffID) ON UPDATE CASCADE ON DELETE CASCADE
)