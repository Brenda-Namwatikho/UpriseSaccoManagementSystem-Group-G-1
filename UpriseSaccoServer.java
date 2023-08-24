import java.sql.Connection;
import java.sql.DriverManager;
import java.sql.PreparedStatement;
import java.sql.ResultSet;
import java.sql.SQLException;
import java.sql.Statement;
import java.time.LocalDate;
import java.time.Period;
import java.time.format.DateTimeFormatter;
import java.util.ArrayList;
import java.util.List;
import java.util.Scanner;
import java.io.File;
import java.io.IOException;
import java.io.ObjectInputStream;
import java.io.ObjectOutputStream;
import java.io.ObjectStreamException;
import java.io.PrintStream;
import java.net.ServerSocket;
import java.net.Socket;

public class SaccoServer {

  static ServerSocket sock;
  static Socket socket;
  static String details[];
  static String serverResponse[] = new String[10];

  public static PreparedStatement databaseConn(String sql, String[] data) throws ClassNotFoundException, SQLException {
    // loading driver
    Class.forName("com.mysql.cj.jdbc.Driver");

    // creating a connection
    Connection conn = DriverManager.getConnection("jdbc:mysql://localhost:3306/uprisesacco", "root", "");

    PreparedStatement prepare = conn.prepareStatement(sql);
    for (int index = 0; index < data.length; index++) {
      prepare.setString(index + 1, data[index]);
    }
    return prepare;

  }

  public static void login1() throws Exception {

    String userdata[] = { details[1], details[2] };
    System.out.println(userdata[0] + "  and  " + userdata[1]);
    String sql = "SELECT password FROM user WHERE memberNumber=? AND contact=? ";
    ResultSet results = databaseConn(sql, userdata).executeQuery();
    String dbpassword = "";
    if (results.next()) {
      dbpassword = results.getString(1);
      serverResponse[0] = "true";
      serverResponse[1] = "Please use this password to login into the system: " + dbpassword;
      ObjectOutputStream out = new ObjectOutputStream(socket.getOutputStream());
      out.writeObject(serverResponse);

    }

    else {
      String memnum = details[1];
      String contact = details[2];
      String refNum = "RF" + memnum.charAt(1) + memnum.charAt(2) + contact.charAt(8) + contact.charAt(7)
          + contact.charAt(6);

      String claim = "the member number entered" + details[1] + "doesnot match the phone number entered " + details[2];
      String[] claimdata = { refNum, "login", claim, details[2], details[1] };
      String sql1 = "INSERT INTO claim(referenceNumber,claimType,claimDescription,contact,memberNumber) VALUES (?,?,?,?,?)";
      databaseConn(sql1, claimdata).executeUpdate();
      serverResponse[0] = "false";
      serverResponse[1] = refNum;
      serverResponse[2] = claim;
      ObjectOutputStream out = new ObjectOutputStream(socket.getOutputStream());
      out.writeObject(serverResponse);

    }
  }

  public static void claim(String[] claim) throws Exception {
    String sql = "SELECT adminComment FROM claim WHERE referenceNumber = ?";
    String[] userdata = { claim[1] };

    ResultSet results = databaseConn(sql, userdata).executeQuery();
    String adminComment = "";
    if (results.next()) {
      System.out.println("Claim is working");
      adminComment = results.getString(1);

      if (adminComment == null) {

        serverResponse[0] = "Sorry!!. The administrator has not yet responded to your claim";
        ObjectOutputStream out = new ObjectOutputStream(socket.getOutputStream());
        out.writeObject(serverResponse);

      } else {
        serverResponse[0] = adminComment;
        ObjectOutputStream out = new ObjectOutputStream(socket.getOutputStream());
        out.writeObject(serverResponse);
      }

    }

  }

  public static void login() throws Exception {
    String userdata[] = { details[1] };
    System.out.println(userdata[0]);
    String sql = "SELECT password, memberNumber FROM user WHERE username = ?";
    ResultSet results = databaseConn(sql, userdata).executeQuery();
    String dbpassword = "";
    String reason = "";
    String memberNumber;
    String applicationID;

    if (results.next()) {
      dbpassword = results.getString(1);
      memberNumber = results.getString(2);
      if (dbpassword.equals(details[2])) {
        String loanstatusquery = "SELECT applicationID FROM loanstatus WHERE memberNumber=? AND status=?";
        String[] loanstatusqueryarray = { memberNumber, "rejected" };
        ResultSet statusquery = databaseConn(loanstatusquery, loanstatusqueryarray).executeQuery();
        if (statusquery.next()) {
          applicationID = statusquery.getString(1);

          String reasonquery = "SELECT reason FROM loanreasons WHERE applicationID=?";
          String[] reasonQueryarray = { applicationID };
          ResultSet rquery = databaseConn(reasonquery, reasonQueryarray).executeQuery();
          if (rquery.next()) {
            reason = rquery.getString(1);
          }
        }
        serverResponse[0] = "true";
        serverResponse[1] = memberNumber;
        serverResponse[2] = reason;
        ObjectOutputStream out = new ObjectOutputStream(socket.getOutputStream());
        out.writeObject(serverResponse);

        return; // Exit the method here
      }
    }

    // If the control reaches this point, it means the if condition was false

    serverResponse[0] = "false";
    ObjectOutputStream out = new ObjectOutputStream(socket.getOutputStream());
    out.writeObject(serverResponse);

  }

  public static double loanProgress(int actualTime, int expectedTime) {
    double progress = ((double) actualTime / expectedTime) * 100;
    return progress;

  }

  public static double contributionPerformance(double totalAmountDeposited, double fixedAmount, int months) {
    double contributionPerformance = (totalAmountDeposited / (fixedAmount * months)) * 100;
    return contributionPerformance;
  }

  public static double loanPerformance(int actualTime, int expectedTime) {
    double loanPerformance = (1 - (((double) actualTime - expectedTime) / expectedTime)) * 100;
    return loanPerformance;

  }

  public static void requestLoan() throws Exception {

    // creating Application Loan Number
    File iny = new File("Number.txt");

    Scanner scann = new Scanner(iny);
    int k = scann.nextInt();
    scann.close();
    iny.delete();

    int memNum = k + 1;
    String ApplicationLoanNumber = String.valueOf(memNum) + "4";

    // incrementing the number in the file by 1
    File ink = new File("Number.txt");
    PrintStream ps = new PrintStream(ink);
    k = k + 1;
    ps.println(k);
    ps.close();

    // getting group number
    File ing = new File("GroupNumber.txt");

    Scanner scand = new Scanner(ing);
    int GroupNumber = scand.nextInt();

    String sql = "INSERT INTO loanapplication (ApplicationID,LoanAmount, PaymentPeriod,ApplicationDate,LoanGroup,MemberNumber)"
        + " VALUE (?,?,?,?,?,?)";
    String squery[] = new String[6];

    squery[0] = ApplicationLoanNumber;
    squery[1] = details[2];
    squery[2] = details[3];
    LocalDate applicationDate = LocalDate.now();
    squery[3] = String.valueOf(applicationDate);
    squery[4] = String.valueOf(GroupNumber);
    squery[5] = details[1];
    int result = databaseConn(sql, squery).executeUpdate();
    if (result > 0) {
      String sql1 = "SELECT COUNT(LoanGroup) FROM loanapplication WHERE LoanGroup = ?";
      // loading Driver
      Class.forName("com.mysql.cj.jdbc.Driver");
      System.out.println("class is successfully loaded");
      // creating a connection
      Connection conn = DriverManager.getConnection("jdbc:mysql://localhost:3306/uprisesacco", "root", "");
      System.out.println("connection is successfully established");
      PreparedStatement prepare = conn.prepareStatement(sql1);
      prepare.setInt(1, GroupNumber);
      ResultSet results1 = prepare.executeQuery();
      results1.next();
      if (results1.getInt(1) == 10) {
        System.out.println(results1.getInt(1));

        // deleting GroupNumber file
        ing.delete();
        // incrementing the number in the file by 1
        File ind = new File("GroupNumber.txt");
        PrintStream p1 = new PrintStream(ind);
        int GroupNumber1 = GroupNumber + 1;
        p1.println(GroupNumber1);
        p1.close();
        System.out.println(GroupNumber);

        processLoan(GroupNumber);
      }

      serverResponse[0] = ApplicationLoanNumber;
      ObjectOutputStream out = new ObjectOutputStream(socket.getOutputStream());
      out.writeObject(serverResponse);

    }

  }

  public static void acceptLoan() throws Exception {
    String[] loanstatus = { details[1] };
    String[] loanStatus = new String[10];
    String loanNumber;
    // creating Loan Number
    File iny = new File("LoanNumber.txt");

    Scanner scann = new Scanner(iny);
    int k = scann.nextInt();
    scann.close();
    iny.delete();

    int memNum = k + 1;
    loanNumber = String.valueOf(memNum);

    // incrementing the number in the file by 1
    File ind = new File("LoanNumber.txt");
    PrintStream p1 = new PrintStream(ind);
    int loanNumber1 = memNum + 1;
    p1.println(loanNumber1);
    p1.close();
    String loansql = "SELECT  amountRequested, amountGiven, amountToReturn, amountToReturnMonthly,receiveDate, paymentPeriod, status FROM loanstatus WHERE applicationID=? ";
    ResultSet loanresults = databaseConn(loansql, loanstatus).executeQuery();
    loanresults.next();
    loanStatus[0] = loanNumber;
    loanStatus[1] = loanresults.getString(1);
    loanStatus[2] = loanresults.getString(2);
    loanStatus[3] = loanresults.getString(3);
    loanStatus[4] = loanresults.getString(4);
    loanStatus[5] = loanresults.getString(5);
    loanStatus[6] = loanresults.getString(6);
    loanStatus[7] = loanresults.getString(1);
    loanStatus[8] = "Active";
    loanStatus[9] = details[2];

    String loansql1 = "INSERT INTO acceptedloan(loanNumber, amountRequested, amountGiven, amountToReturn, amountToReturnMonthly,receiveDate, paymentPeriod, loanBalance, status, memberNumber )"
        +
        "VALUES(?,?,?,?,?,?,?,?,?,?)";
    databaseConn(loansql1, loanStatus).executeUpdate();
    String[] LoanNumber = { "accepted", loanNumber };
    ObjectOutputStream out = new ObjectOutputStream(socket.getOutputStream());
    out.writeObject(LoanNumber);

  }

  public static void rejectLoan() throws Exception {
    String groupNum[] = new String[2];
    String sql1 = "SELECT loanGroup, amountGiven FROM loanstatus WHERE applicationID=?";
    String sql1Array[] = { details[1] };
    ResultSet group = databaseConn(sql1, sql1Array).executeQuery();
    if (group.next()) {
      groupNum[0] = group.getString(1);
      groupNum[1] = group.getString(2);
    }
    String sql2 = "SELECT memberNumber, amountRequested, amountGiven,applicationID,paymentPeriod FROM loanstatus WHERE loanGroup=? AND status!=? AND applicationID!=?";
    String sql3 = "SELECT 0.75*TotalDeposits FROM user WHERE memberNumber=?";
    String sql2Array[] = { groupNum[0], "rejected", details[1] };
    ArrayList<Object[]> loandist = new ArrayList<>();
    ResultSet rows = databaseConn(sql2, sql2Array).executeQuery();
    double AmountToGetbalance = 0;
    double AmountRequestedbalance = 0;
    while (rows.next()) {
      Object loans[] = new Object[8];
      loans[0] = rows.getString(1);
      loans[1] = rows.getDouble(2);
      loans[2] = rows.getDouble(3);
      loans[3] = rows.getString(4);
      String sql3Array[] = { rows.getString(1) };
      ResultSet totaldeposit = databaseConn(sql3, sql3Array).executeQuery();
      totaldeposit.next();
      loans[4] = totaldeposit.getDouble(1);
      loans[5] = -(((double) loans[2]) - ((double) loans[4]));
      loans[6] = -(((double) loans[1]) - ((double) loans[4]));
      loans[7] = rows.getInt(5);

      loandist.add(loans);
      AmountToGetbalance = AmountToGetbalance + ((double) loans[5]);
      AmountRequestedbalance = AmountRequestedbalance + ((double) loans[6]);

    }
    double TotalamountDist = 0;
    double newAmount = 0.0;
    for (int k = 0; k < loandist.size(); k++) {
      Object[] loanData = loandist.get(k);

      if (((double) loanData[5]) > 0.0) {

        if (Double.parseDouble(groupNum[1]) >= AmountToGetbalance) {

          System.out.println("(" + ((double) loanData[4]) + "<" + ((double) loanData[2]) + ")" + "&&" + "("
              + ((double) loanData[4]) + ">" + ((double) loanData[2]) + ")");
          if ((((double) loanData[4]) >= ((double) loanData[1])) && (((double) loanData[1]) > ((double) loanData[2]))) {
            newAmount = ((double) loanData[4]) + (((double) loanData[1]) - ((double) loanData[4]));
            TotalamountDist = TotalamountDist + (((double) loanData[1]) - ((double) loanData[4]));
            System.out.println("newAmount: " + newAmount + "     " + "Amount distributed: " + TotalamountDist);
          } else if ((((double) loanData[4]) < ((double) loanData[1]))
              && (((double) loanData[4]) > ((double) loanData[2]))) {

            newAmount = ((double) loanData[2]) + (((double) loanData[4]) - ((double) loanData[2]));
            TotalamountDist = TotalamountDist + (((double) loanData[4]) - ((double) loanData[2]));
            System.out.println("newAmount: " + newAmount + "     " + "Amount distributed: " + TotalamountDist);
          }

        } else {

          if ((((double) loanData[4]) >= ((double) loanData[1])) && (((double) loanData[1]) > ((double) loanData[2]))) {

            newAmount = ((double) loanData[2])
                + ((0.3 * (((double) loanData[4]) - ((double) loanData[2])) / AmountToGetbalance)
                    + (0.7 * (((double) loanData[1]) - ((double) loanData[2])) / AmountRequestedbalance))
                    * AmountToGetbalance;
            TotalamountDist = TotalamountDist
                + ((0.3 * (((double) loanData[4]) - ((double) loanData[2])) / AmountToGetbalance)
                    + (0.7 * (((double) loanData[1]) - ((double) loanData[2])) / AmountRequestedbalance))
                    * AmountToGetbalance;
            System.out.println("newAmount: " + newAmount + "     " + "Amount distributed: " + TotalamountDist);
          } else if ((((double) loanData[4]) < ((double) loanData[1]))
              && (((double) loanData[4]) > ((double) loanData[2]))) {
            newAmount = ((double) loanData[2])
                + ((0.7 * (((double) loanData[4]) - ((double) loanData[2])) / AmountToGetbalance)
                    + (0.3 * (((double) loanData[1]) - ((double) loanData[2])) / AmountRequestedbalance))
                    * AmountToGetbalance;

            TotalamountDist = TotalamountDist
                + ((0.7 * (((double) loanData[4]) - ((double) loanData[2])) / AmountToGetbalance)
                    + (0.3 * (((double) loanData[1]) - ((double) loanData[2])) / AmountRequestedbalance))
                    * AmountToGetbalance;
            System.out.println("newAmount: " + newAmount + "     " + "Amount distributed: " + TotalamountDist);
          }
        }
        // manipulate the loanstatus table for updates.
        double amountToReturnMonthly;
        double amountToReturn;
        if (((int) loanData[7]) <= 2) {
          amountToReturn = newAmount + (newAmount * 0.05 * ((int) loanData[7]) / 12);
          amountToReturnMonthly = amountToReturn / ((int) loanData[7]);
        } else if ((((int) loanData[7]) > 2) && (((int) loanData[7]) <= 4)) {
          amountToReturn = newAmount + (newAmount * 0.08 * ((int) loanData[7]) / 12);
          amountToReturnMonthly = amountToReturn / ((int) loanData[7]);
        } else if ((((int) loanData[7]) > 5) && (((int) loanData[7]) <= 6)) {
          amountToReturn = newAmount + (newAmount * 0.1 * ((int) loanData[7]) / 12);
          amountToReturnMonthly = amountToReturn / ((int) loanData[7]);
        } else if ((((int) loanData[7]) > 6) && (((int) loanData[7]) <= 8)) {
          amountToReturn = newAmount + (newAmount * 0.12 * ((int) loanData[2]) / 12);
          amountToReturnMonthly = amountToReturn / ((int) loanData[2]);
        } else {
          amountToReturn = newAmount + (newAmount * 0.15 * ((int) loanData[7]) / 12);
          amountToReturnMonthly = amountToReturn / ((int) loanData[7]);
        }

        String sql = "UPDATE loanstatus SET amountGiven=?, amountTOReturn=?, amountToReturnMonthly=? WHERE applicationID=?";
        String sqlArray[] = { String.valueOf(newAmount), String.valueOf(amountToReturn),
            String.valueOf(amountToReturnMonthly), (String) loanData[3] };
        databaseConn(sql, sqlArray).executeUpdate();

      }
    }
    // manipulate the saccoinfo table for updates.
    double balance = Double.parseDouble(groupNum[1]) - TotalamountDist;
    String sql6 = "UPDATE saccoinfo SET availableFunds=?+availableFunds WHERE saccoID=?";
    String sql6Array[] = { String.valueOf(balance), "1001" };
    databaseConn(sql6, sql6Array).executeUpdate();
    String[] serverResponse = { "We are sorry our member for not helping you in this situation" };
    ObjectOutputStream out = new ObjectOutputStream(socket.getOutputStream());
    out.writeObject(serverResponse);
  }

  public static void processLoan(int gnumber) throws Exception {
    // retrieving loan requests and available funds from the database
    Object loanRequests[][] = new Object[10][13];
    String sql = "SELECT applicationID, loanAmount, paymentPeriod, memberNumber FROM loanapplication WHERE loanGroup=?";
    String funds = "SELECT availableFunds FROM saccoinfo ";
    Class.forName("com.mysql.cj.jdbc.Driver");
    System.out.println("class is successfully loaded");
    // creating a connection
    Connection conn = DriverManager.getConnection("jdbc:mysql://localhost:3306/uprisesacco", "root", "");
    System.out.println("connection is successfully established");
    PreparedStatement prepare = conn.prepareStatement(sql);
    PreparedStatement prepary = conn.prepareStatement(funds);
    prepare.setInt(1, gnumber);
    ResultSet results = prepare.executeQuery();
    ResultSet FundsResults = prepary.executeQuery();
    FundsResults.next();

    // available funds for the sacco
    double availableFunds = FundsResults.getDouble(1);
    System.out.println(availableFunds);
    // Actual funds available to distribute to a particular group.

    double actualFunds = availableFunds - 2000000;
    System.out.println(actualFunds);
    for (int i = 0; i < 10 && results.next(); i++) {

      loanRequests[i][0] = results.getInt(1);
      loanRequests[i][1] = results.getDouble(2);
      loanRequests[i][2] = results.getInt(3);
      loanRequests[i][3] = results.getInt(4);

    }

    // retrieving total deposits, date joined and amount to pay monthly
    Statement contribution = conn.createStatement();
    for (int j = 0; j < 10; j++) {
      String user = "SELECT dateJoined, totalDeposits*0.75, amountExpectedToDepositMonthly FROM user WHERE memberNumber="
          + Integer.parseInt(loanRequests[j][3].toString()) + "";

      ResultSet contribute = contribution.executeQuery(user);
      if (contribute.next()) {
        loanRequests[j][10] = contribute.getDate(1);
        loanRequests[j][11] = contribute.getDouble(2);
        loanRequests[j][12] = contribute.getDouble(3);
      }
    }

    // checking if avaliable funds are sufficent to give loans
    if (availableFunds > 2000000) {

      // retrieving previous loan status and available funds from the database
      String prevLoan = "SELECT loanNumber, amountToReturnMonthly, receiveDate, paymentPeriod, completionDate, status  FROM acceptedloan WHERE memberNumber=?";
      PreparedStatement Loanprepare = conn.prepareStatement(prevLoan);

      for (int i = 0; i < 10; i++) {
        Loanprepare.setInt(1, Integer.parseInt(loanRequests[i][3].toString()));
        ResultSet previousLoans = Loanprepare.executeQuery();
        if (previousLoans.next()) {
          loanRequests[i][4] = previousLoans.getInt(1);
          loanRequests[i][5] = previousLoans.getDouble(2);
          loanRequests[i][6] = previousLoans.getDate(3);
          loanRequests[i][7] = previousLoans.getInt(4);
          loanRequests[i][8] = previousLoans.getDate(5);
          loanRequests[i][9] = previousLoans.getString(6);
        }
      }

      // checking if a member have an active loan
      ArrayList<Object[]> loanRequest1 = new ArrayList<>();
      for (int index = 0; index < 10; index++) {
        if (loanRequests[index][9] != null && loanRequests[index][9].equals("Active")) {

          String loanreject = "INSERT INTO loanstatus(applicationID,loanGroup, amountRequested, amountGiven, amountToReturn, amountToReturnMonthly, paymentPeriod, status, memberNumber  )"
              +
              " VALUES(" + Integer.parseInt(loanRequests[index][3].toString()) + "," + gnumber + "," + 0 + "," + 0 +
              "," + 0 + "," + 0 + "," + 0 + ",'rejected'," + Integer.parseInt(loanRequests[index][3].toString()) + ")";
          Statement loanStatement = conn.createStatement();
          loanStatement.executeUpdate(loanreject);
          String loanreject1 = "INSERT INTO loanreasons(ApplicationID, memberNumber, reason  )" +
              " VALUES(" + Integer.parseInt(loanRequests[index][3].toString()) + ","
              + Integer.parseInt(loanRequests[index][3].toString())
              + ",'Sorry, you are not qualified for another loan because you still have an active loan.')";
          Statement loanStatement1 = conn.createStatement();
          loanStatement1.executeUpdate(loanreject1);

        } else {
          loanRequest1.add(loanRequests[index]);

        }
      }
      // processing loan
      int length = loanRequest1.size();
      double totalAmountToLendOut = 0;
      double totalAmountRequested = 0;
      // getting total amount requested by a group and total amount a group can get
      for (int k = 0; k < length; k++) {
        Object[] loanData = loanRequest1.get(k);
        totalAmountToLendOut = totalAmountToLendOut + (double) loanData[11];
        totalAmountRequested = totalAmountRequested + (double) loanData[1];

      }

      // giving out loans

      double amountGiven = 0;
      double totalAmountGivenOut = 0;
      for (int x = 0; x < length; x++) {
        Object[] loanData = loanRequest1.get(x);
        LocalDate currentdate = LocalDate.now();
        // getting number of months a person has spent in sacco

        if (loanData[4] != null) {
          // giving loan to people that have ever got a loan when totalAmount to lend out
          // is greater than actual funds

          // giving loan when totalAmountToLendOut is lessthan or equal to actualFunds and
          // 3/4ofdeposit is greater than requested amount.
          if ((totalAmountToLendOut <= actualFunds) && (((double) loanData[11]) > ((double) loanData[1]))) {
            amountGiven = ((double) loanData[1]);
          }
          // giving loan when totalAmountToLendOut is lessthan or equal to actualFunds and
          // 3/4ofdeposit is lessthan requested amount.
          if ((totalAmountToLendOut <= actualFunds) && (((double) loanData[11]) < ((double) loanData[1]))) {
            amountGiven = ((double) loanData[11]);
          }
          // giving loan when totalAmountToLendOut is greater than actualFunds and
          // 3/4ofdeposit is greater than requested amount.
          if ((totalAmountToLendOut > actualFunds) && (((double) loanData[11]) > ((double) loanData[1]))) {
            amountGiven = ((0.351 * ((double) loanData[11]) / totalAmountToLendOut)
                + (0.649 * ((double) loanData[1]) / totalAmountRequested)) * actualFunds;
          }
          // giving loan when totalAmountToLendOut is greater than actualFunds and
          // 3/4ofdeposit is lessthan requested amount.
          if ((totalAmountToLendOut > actualFunds) && (((double) loanData[11]) < ((double) loanData[1]))) {
            amountGiven = ((0.649 * ((double) loanData[11]) / totalAmountToLendOut)
                + (0.351 * ((double) loanData[1]) / totalAmountRequested)) * actualFunds;
          }

        } else {
          // giving loan to people for the first time when totalAmount to lend out is
          // greater than actual funds
          // giving loan when totalAmountToLendOut is lessthan or equal to actualFunds and
          // 3/4ofdeposit is greater than requested amount.
          if ((totalAmountToLendOut <= actualFunds) && (((double) loanData[11]) > ((double) loanData[1]))) {
            amountGiven = ((double) loanData[1]);
          }
          // giving loan when totalAmountToLendOut is lessthan or equal to actualFunds and
          // 3/4ofdeposit is lessthan requested amount.
          if ((totalAmountToLendOut <= actualFunds) && (((double) loanData[11]) < ((double) loanData[1]))) {
            amountGiven = ((double) loanData[11]);
          }
          // giving loan when totalAmountToLendOut is greater than actualFunds and
          // 3/4ofdeposit is greater than requested amount.
          if ((totalAmountToLendOut > actualFunds) && (((double) loanData[11]) > ((double) loanData[1]))) {
            amountGiven = ((0.351 * ((double) loanData[11]) / totalAmountToLendOut)
                + (0.649 * ((double) loanData[1]) / totalAmountRequested)) * actualFunds;
          }
          // giving loan when totalAmountToLendOut is greater than actualFunds and
          // 3/4ofdeposit is lessthan requested amount.
          if ((totalAmountToLendOut > actualFunds) && (((double) loanData[11]) < ((double) loanData[1]))) {
            amountGiven = ((0.649 * ((double) loanData[11]) / totalAmountToLendOut)
                + (0.351 * ((double) loanData[1]) / totalAmountRequested)) * actualFunds;
          }
        }
        totalAmountGivenOut = totalAmountGivenOut + amountGiven;
        System.out.println("total amount given out" + totalAmountGivenOut);
        // calculating loan details
        double amountToReturnMonthly;
        double amountToReturn;
        if (((int) loanData[2]) <= 2) {
          amountToReturn = amountGiven + (amountGiven * 0.05 * ((int) loanData[2]) / 12);
          amountToReturnMonthly = amountToReturn / ((int) loanData[2]);
        } else if ((((int) loanData[2]) > 2) && (((int) loanData[2]) <= 4)) {
          amountToReturn = amountGiven + (amountGiven * 0.08 * ((int) loanData[2]) / 12);
          amountToReturnMonthly = amountToReturn / ((int) loanData[2]);
        } else if ((((int) loanData[2]) > 5) && (((int) loanData[2]) <= 6)) {
          amountToReturn = amountGiven + (amountGiven * 0.1 * ((int) loanData[2]) / 12);
          amountToReturnMonthly = amountToReturn / ((int) loanData[2]);
        } else if ((((int) loanData[2]) > 6) && (((int) loanData[2]) <= 8)) {
          amountToReturn = amountGiven + (amountGiven * 0.12 * ((int) loanData[2]) / 12);
          amountToReturnMonthly = amountToReturn / ((int) loanData[2]);
        } else {
          amountToReturn = amountGiven + (amountGiven * 0.15 * ((int) loanData[2]) / 12);
          amountToReturnMonthly = amountToReturn / ((int) loanData[2]);
        }

        // storing details to the database
        String formattedDate = currentdate.format(DateTimeFormatter.ofPattern("yyyy-MM-dd"));
        String processedloan = "INSERT INTO loanstatus VALUES(" + ((int) loanData[0]) + "," + gnumber + ","
            + ((double) loanData[1]) + "," + amountGiven + "," + amountToReturn + "," + amountToReturnMonthly + ",'"
            + formattedDate + "'," + ((int) loanData[2]) + ",'Not Approved'," + ((int) loanData[3]) + ")";

        Statement loanprocess = conn.createStatement();
        loanprocess.executeUpdate(processedloan);
      }
      // updating available funds
      double newfunds = availableFunds - totalAmountGivenOut;
      String newfund = "UPDATE saccoinfo SET availableFunds=" + newfunds + " WHERE saccoID=" + 1001 + "";
      Statement reduce = conn.createStatement();
      reduce.executeUpdate(newfund);

    } else {
      // insuficient available funds scenario
      for (int index = 0; index < 10; index++) {
        if (loanRequests[index][9] != null && loanRequests[index][9].equals("Active")) {
          String loanreject = "INSERT INTO loanstatus(applicationID, amountRequested, amountGiven, amountToReturn, amountToReturnMonthly, paymentPeriod, status, memberNumber  )"
              +
              " VALUES(" + Integer.parseInt(loanRequests[index][3].toString()) + "," + 0 + "," + 0 + "," + 0 + "," + 0
              + "," + 0 + ",'rejected'," + Integer.parseInt(loanRequests[index][3].toString()) + ")";
          Statement loanStatement = conn.createStatement();
          loanStatement.executeUpdate(loanreject);
          String loanreject1 = "INSERT INTO loanreasons(ApplicationID, memberNumber, reason  )" +
              " VALUES(" + Integer.parseInt(loanRequests[index][3].toString()) + ","
              + Integer.parseInt(loanRequests[index][3].toString())
              + ",'Sorry, we are unable to process your loan because the sacco has insufficient funds.')";
          Statement loanStatement1 = conn.createStatement();
          loanStatement1.executeUpdate(loanreject1);

        }
      }
    }
  }

  public static void LoanRequestStatus() throws Exception {

    String[] loanstatus = { details[1] };
    String[] loanStatus = new String[7];
    String loansql = "SELECT  amountRequested, amountGiven, amountToReturn, amountToReturnMonthly,receiveDate, paymentPeriod, status FROM loanstatus WHERE applicationID=? ";
    ResultSet loanresults = databaseConn(loansql, loanstatus).executeQuery();
    if (loanresults.next()) {
      loanStatus[0] = loanresults.getString(1);
      loanStatus[1] = loanresults.getString(2);
      loanStatus[2] = loanresults.getString(3);
      loanStatus[3] = loanresults.getString(4);
      loanStatus[4] = loanresults.getString(5);
      loanStatus[5] = loanresults.getString(6);
      loanStatus[6] = loanresults.getString(7);
    }
    if (loanStatus[6].equals("Not Approved")) {
      String loanStat[] = { "Your loan was processed. please wait for the admin to approve" };
      ObjectOutputStream out = new ObjectOutputStream(socket.getOutputStream());
      out.writeObject(loanStat);
    }
    ObjectOutputStream out = new ObjectOutputStream(socket.getOutputStream());
    out.writeObject(loanStatus);
  }

  /*
   * 
   * private static void processDeposit(String[] depositdata) {
   * 
   * 
   * if (rs.next()) {
   * String memberNumber = rs.getString("memberNumber");
   * String depositedDate = rs.getString("datedeposited");
   * 
   * // Retrieve the total_deposits from the user table
   * String totalDepositsQuery =
   * "SELECT totalDeposits FROM user WHERE memberNumber = ?";
   * PreparedStatement totalDepositsStmt =
   * conn.prepareStatement(totalDepositsQuery);
   * totalDepositsStmt.setString(1, memberNumber);
   * ResultSet totalDepositsRs = totalDepositsStmt.executeQuery();
   * totalDepositsRs.next();
   * String newBalance = totalDepositsRs.getString("totalDeposits");
   * String[] serverResponse = { memberNumber, depositdata[1], depositedDate,
   * newBalance };
   * ObjectOutputStream out = new ObjectOutputStream(socket.getOutputStream());
   * out.writeObject(serverResponse);
   * 
   * } else {
   * String[] serverResponse = {
   * "Deposit details do not match. Please check again later when data is updated."
   * };
   * ObjectOutputStream out = new ObjectOutputStream(socket.getOutputStream());
   * out.writeObject(serverResponse);
   * 
   * }
   * } catch (SQLException e) {
   * e.getMessage();
   * } catch (ObjectStreamException y) {
   * y.printStackTrace();
   * } catch (IOException x) {
   * x.printStackTrace();
   * }
   * }
   */
  private static void processDeposit(String[] depositdata) throws Exception {
    ObjectOutputStream out = new ObjectOutputStream(socket.getOutputStream());

    String[] deposits = new String[4];

    String query = "SELECT * FROM deposit WHERE receiptnumber = ? AND datedeposited = ? AND amount = ?";
    String depositQuery[] = { depositdata[3], depositdata[2], depositdata[1] };
    ResultSet depoSet = databaseConn(query, depositQuery).executeQuery();
    if (depoSet.next()) {

      deposits[0] = depoSet.getString("memberNumber");
      deposits[1] = depoSet.getString("amount");
      deposits[2] = depoSet.getString("datedeposited");
      // Retrieve the total_deposits from the user table
      String totalDepositsQuery = "SELECT totalDeposits FROM user WHERE memberNumber = ?";
      String totaldepositArray[] = { deposits[0] };
      ResultSet total = databaseConn(totalDepositsQuery, totaldepositArray).executeQuery();
      total.next();
      deposits[3] = total.getString("totalDeposits");
      // Send the bool array back to the client
      out.writeObject(deposits);
    } else {
      // Handle the case when deposit details don't match
      String errorMessage = "*************************************************************************\n"
          + "Deposit details do not match. Please check again later when data is updated.\n"
          + "*************************************************************************";

      System.out.println(errorMessage);

      // Send the error message back to the client as an array
      String[] errorArray = { errorMessage };
      out.writeObject(errorArray);
    }

  }

  public static void checkStatement(String[] userdata) throws Exception {
    System.out.println(details[0] + "  " + details[1] + "  " + details[2] + "  " + details[3] + "  " + details[4]);

    // Create arrays to store the data
    String[][] bool = new String[6][1];
    String[][] bool2 = new String[2][1];

    if (userdata[4].equals("loan")) {

      String query1 = "SELECT loanNumber, amountToReturn, loanBalance, receiveDate, paymentPeriod FROM acceptedloan WHERE memberNumber = ?";
      String query1Array[] = { userdata[1] };
      ResultSet rs1 = databaseConn(query1, query1Array).executeQuery();
      // Execute the query
      ArrayList<String[][]> loandata = new ArrayList<>();
      if (rs1.next()) {

        bool[0][0] = rs1.getString("loanNumber");
        bool[1][0] = rs1.getString("amountToReturn");
        bool[2][0] = rs1.getString("loanBalance");
        System.out.println(
            "Loan number " + bool[0][0] + "Amount to return " + bool[1][0] + "  " + "loan balance " + bool[2][0]);

        // Calculate amountPaid
        double amountToReturn = Double.parseDouble(rs1.getString("amountToReturn"));
        double loanBalance = Double.parseDouble(rs1.getString("loanBalance"));
        double amountPaid = amountToReturn - loanBalance;
        bool[3][0] = String.valueOf(amountPaid);
        System.out.println("Amount Paid " + bool[3][0]);

        // Calculate timeTaken in months
        String dateReceivedStr = rs1.getString("receiveDate");
        LocalDate dateReceived = LocalDate.parse(dateReceivedStr);
        LocalDate currentDate = LocalDate.now();
        Period period = Period.between(dateReceived, currentDate);
        int timeTaken = period.getMonths();
        System.out.println("Time taken " + timeTaken);

        // Calculate loanProgress
        int paymentTime = Integer.parseInt(rs1.getString("paymentPeriod"));
        double loanProgressCalculation = loanProgress(timeTaken, paymentTime);
        System.out.println("loan progress " + String.valueOf(loanProgressCalculation));
        bool[4][0] = String.valueOf(loanProgressCalculation);

        // Calculate loanPerformance

        System.out.println("Payment Period " + paymentTime);
        double loanPerformanceCalculation = loanPerformance(timeTaken, paymentTime);
        bool[5][0] = String.valueOf(loanPerformanceCalculation);
        System.out.println("loan performance " + bool[5][0]);

        loandata.add(0, bool);

      } else {
        // Create a message array
        String[][] noLoanMessageArray = { { "No loan record found" } };
        loandata.add(0, noLoanMessageArray);
      }

      String query2 = "SELECT paymentDate, amountPaid FROM loanPayment WHERE loanNumber = ? AND paymentDate BETWEEN ? AND ?";
      String query2Array[] = { bool[0][0], userdata[2], userdata[3] };
      ResultSet rs2 = databaseConn(query2, query2Array).executeQuery();
      // Create a list to hold each row of data
      List<String[]> paymentDataList = new ArrayList<>();
      boolean hasPaymentData = false; // Flag to track if payment data exists
      while (rs2.next()) {
        hasPaymentData = true; // Set the flag to true since data is found

        String paymentDate = rs2.getString("paymentDate");
        String amountPaid = rs2.getString("amountPaid");

        // Create an array to hold the data of each row
        String[] rowData = { paymentDate, amountPaid };

        // Add the row data to the list
        paymentDataList.add(rowData);

      }

      // If no payment data was found, add a message to the paymentDataList
      if (!hasPaymentData) {
        String[] noPaymentDataMessage = { "No payment data found" };
        paymentDataList.add(noPaymentDataMessage);
      }

      // Convert the list to a two-dimensional array
      String[][] paymentDataArray = new String[paymentDataList.size()][];
      for (int i = 0; i < paymentDataList.size(); i++) {
        paymentDataArray[i] = paymentDataList.get(i);
      }
      // Print the data from the array (or handle it as needed)
      for (int i = 0; i < paymentDataArray.length; i++) {
        String[] rowData = paymentDataArray[i];
        if (rowData.length == 1 && rowData[0].equals("No payment data found")) {
          System.out.println(rowData[0]); // Print the message
        } else {
          System.out.println("Payment Date: " + rowData[0] + ", Amount Paid: " + rowData[1]);
        }
      }

      // Add paymentDataArray to the loandata ArrayList
      loandata.add(1, paymentDataArray);

      // Send the bool array back to the client
      ObjectOutputStream out = new ObjectOutputStream(socket.getOutputStream());
      out.writeObject(loandata);
      // System.out.println(loa);
      return;
    }

    // Retrieve totalDeposits, dateJoined, and
    // amountExpectedToDepositMonthly from the user table
    if (userdata[4].equals("deposit")) {

      String query3 = "SELECT amount, datedeposited FROM deposit WHERE memberNumber = ? AND datedeposited BETWEEN ? AND ?";
      String query3Array[] = { userdata[1], userdata[2], userdata[3] };
      ResultSet rs3 = databaseConn(query3, query3Array).executeQuery();

      // Create a list to hold each row of data
      List<String[]> depositList = new ArrayList<>();
      boolean hasDepositData = false; // Flag to track if deposit data exist
      while (rs3.next()) {
        hasDepositData = true; // Set the flag to true since data is found
        String datedeposited = rs3.getString("datedeposited");
        String amount = rs3.getString("amount");

        // Create an array to hold the data of each row
        String[] rowData = { datedeposited, amount };

        // Add the row data to the list
        depositList.add(rowData);

      }
      // If no deposit data was found, add a message to the depositList
      if (!hasDepositData) {
        String[] noDepositDataMessage = { "No deposit data found" };
        depositList.add(noDepositDataMessage);
      }

      // Convert the list to a two-dimensional array
      String[][] depositArray = new String[depositList.size()][];
      for (int i = 0; i < depositList.size(); i++) {
        depositArray[i] = depositList.get(i);
      }
      // Print the data from the array (or handle it as needed)
      for (int i = 0; i < depositArray.length; i++) {
        String[] rowData = depositArray[i];
        if (rowData.length == 1 && rowData[0].equals("No deposit data found")) {
          System.out.println(rowData[0]); // Print the message
        } else {
          System.out.println("Deposit Date: " + rowData[0] + ", Amount: " + rowData[1]);
        }
      }

      String query4 = "SELECT totalDeposits, dateJoined, amountExpectedToDepositMonthly FROM user WHERE memberNumber = ?";
      String query4Array[] = { userdata[1] };
      ResultSet rs4 = databaseConn(query4, query4Array).executeQuery();

      if (rs4.next()) {

        bool2[0][0] = rs4.getString("totalDeposits");
        System.out.println("Total deposits" + " " + bool2[0][0]);

        // Calculate monthsStayed in months
        String dateJoinedStr = rs4.getString("dateJoined");
        LocalDate dateJoined = LocalDate.parse(dateJoinedStr);
        Period period = Period.between(dateJoined, LocalDate.now());
        int monthsStayed = period.getMonths();
        System.out.println(monthsStayed);

        // Calculate contributionPerformance
        double amountExpectedToDepositMonthly = Double
            .parseDouble(rs4.getString("amountExpectedToDepositMonthly"));
        double contributionCalculation = contributionPerformance(
            Double.parseDouble(rs4.getString("totalDeposits")),
            amountExpectedToDepositMonthly, monthsStayed);
        bool2[1][0] = String.valueOf(contributionCalculation);
        System.out.println("Contribution Performance " + bool2[1][0]);
      }
      ArrayList<String[][]> depositdata = new ArrayList<>();
      depositdata.add(0, depositArray);
      depositdata.add(1, bool2);
      // Send the bool array back to the client
      ObjectOutputStream out = new ObjectOutputStream(socket.getOutputStream());
      out.writeObject(depositdata);
      return;
    }

  }

  public static void main(String[] args) throws Exception {
    // starting server and reading data from socket
    sock = new ServerSocket(3333);
    System.out.println("SERVER STARTED.............");

    while (true) {
      // Accept client connection
      socket = sock.accept();
      ObjectInputStream ind = new ObjectInputStream(socket.getInputStream());
      details = (String[]) ind.readObject();
      System.out.println(details[0]);

      // Handle client request based on details[0]

      if (details[0].equalsIgnoreCase("login")) {
        login();
      } else if (details[0].equalsIgnoreCase("login1")) {
        login1();
      } else if (details[0].equalsIgnoreCase("deposit")) {
        processDeposit(details);
      } else if (details[0].equalsIgnoreCase("claim")) {
        claim(details);
      } else if (details[0].equalsIgnoreCase("checkStatement")) {
        checkStatement(details);
      } else if (details[0].equalsIgnoreCase("requestLoan")) {
        requestLoan();
      } else if (details[0].equalsIgnoreCase("LoanRequestStatus")) {
        LoanRequestStatus();
      } else if (details[0].equalsIgnoreCase("acceptLoan")) {
        acceptLoan();
      } else if (details[0].equalsIgnoreCase("rejectLoan")) {
        rejectLoan();
      }
      // Close the socket
      socket.close();
    }
  }

}