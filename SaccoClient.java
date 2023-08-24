import java.io.ObjectInputStream;
import java.io.ObjectOutputStream;
import java.net.Socket;
import java.util.ArrayList;
import java.util.Scanner;

public class SaccoClient {

    static Scanner output;
    static Scanner inputd = new Scanner(System.in);
    static String memberNumber;

    public static String[] socketMethod(String[] clientRequest) throws Exception {
        Socket socket = new Socket("10.10.128.206", 3333);

        ObjectOutputStream out = new ObjectOutputStream(socket.getOutputStream());
        out.writeObject(clientRequest);

        ObjectInputStream ins = new ObjectInputStream(socket.getInputStream());
        String serverResponse[] = (String[]) ins.readObject();

        socket.close();
        out.close();
        return serverResponse;
    }

    public static ArrayList<String[][]> socketArrayList(String[] clientRequest) throws Exception {
        Socket arrayList = new Socket("127.0.0.1", 3333);
        System.out.println("connection established");
        ObjectOutputStream out = new ObjectOutputStream(arrayList.getOutputStream());
        out.writeObject(clientRequest);

        ObjectInputStream ins = new ObjectInputStream(arrayList.getInputStream());

        ArrayList<String[][]> serverResponse = (ArrayList<String[][]>) ins.readObject();

        arrayList.close();
        out.close();
        return serverResponse;
    }

    private static void showMainMenu() throws Exception {
        System.out.println("UPRISE SACCO MANAGEMENT SYSTEM");
        System.out.println("-----------------MAIN MENU------------------------------------------------------");
        System.out.println("Action                  | Command");
        System.out.println("--------------------------------------------------------------------------------");
        System.out.println("Deposit                 | deposit amount date_deposited receipt_number");
        System.out.println("Check statement         | CheckStatement dateFrom DateTo");
        System.out.println("Request loan            | requestLoan amount paymentPeriod_in_months");
        System.out.println("Check loan status       | LoanRequestStatus loan_application_number");
        System.out.println("--------------------------------------------------------------------------------");
        Scanner input = new Scanner(System.in);
        String command = input.nextLine();
        String[] actions = command.split(" ");
        if (actions[0].equalsIgnoreCase("deposit")) {
            deposit(actions);

        } else if (actions[0].equalsIgnoreCase("checkStatement")) {
            checkStatement(actions);

        }
        if (actions[0].equalsIgnoreCase("requestLoan")) {
            requestLoan(actions);

        } else if (actions[0].equalsIgnoreCase("LoanRequestStatus")) {
            loanRequestStatus(actions);

        }
        if (actions[0].equalsIgnoreCase("loanProgress")) {
            loanProgress();

        } else if (actions[0].equalsIgnoreCase("payLoan")) {
            payLoan();

        } else {
            // System.out.println("Invalid command: Check the main menu");
            showMainMenu();
        }

    }

    public static void login() throws Exception {

        String userdata1[] = new String[3];
        String userdata[] = new String[3];
        userdata[0] = "login";

        System.out.println("Enter your command in this format: Login 'your_username' 'your_password'");
        System.out.println("\n -------------------------------------------OR---------------------------------------");
        System.out
                .println("Enter your command in this format to view your claim status: Claim 'your_reference_number'");
        String login = inputd.nextLine();
        userdata = login.split(" ");

        String bool[] = socketMethod(userdata);
        memberNumber = bool[1];
        if (bool[0].equals("true")) {
            System.out.println(bool[2]);
            showMainMenu();
        } else if (bool[0].equals("false")) {
            System.out
                    .println("---------------------------invalid username or password----------------------------");
            System.out
                    .println("---------Please enter your member Number and phone number to get a password--------");
            String login2[] = { "Member Number", "Phone Number" };

            for (int index = 0; index < login2.length; index++) {

                userdata1[0] = "login1";
                System.out.println("Enter your " + login2[index]);
                userdata1[index + 1] = inputd.nextLine();
            }
            String bool2[] = socketMethod(userdata1);
            if (bool2[0].equals("true")) {
                System.out.println("--------" + bool2[1] + "---------");
                login();

            } else {
                System.out.println("Sorry, you are unable to login into in the System.");
                System.out.println("Please here is the reference number (" + bool2[1]
                        + ") you should use to next time to know the status of the problem");
            }
        } else {
            System.out.println(bool[0]);
        }
    }

    public static void checkStatement(String[] statement) throws Exception {

        String userdata[] = new String[5];
        userdata[0] = "checkStatement";
        userdata[1] = memberNumber;
        userdata[2] = statement[1];
        userdata[3] = statement[2];
        userdata[4] = "deposit";
        String userdata1[] = new String[5];
        userdata1[0] = "checkStatement";
        userdata1[1] = memberNumber;
        userdata1[2] = statement[1];
        userdata1[3] = statement[2];
        userdata1[4] = "loan";

        ArrayList<String[][]> bool = socketArrayList(userdata1);
        ArrayList<String[][]> bool2 = socketArrayList(userdata);

        // Print the header and the rest of your existing code to display the results
        System.out.println("                        UPRISE SACCO MANAGEMENT SYSTEM");
        System.out.println(
                "-----------------------------------------------------------------------------------------------------------------");

        // Print Loan Details
        System.out.println("                               Loan Details");

        // Assuming the data is present in the bool array

        String[][] data = bool.get(0);
        if (data[0][0].equals("No loan record found")) {
            System.out.println(
                    "|----------------------------------------------------------------------------------------|");
            // Print the message
            System.out.println("You do not have a loan record.");
            System.out.println(
                    "|----------------------------------------------------------------------------------------|");

        } else {
            System.out.println(
                    "|----------------|------------|--------|--------------|----------------------------------|");
            System.out.println("Amount to return  Amount Paid  Balance  Loan Progress  Loan Performance");
            System.out.println(
                    "|----------------|------------|--------|---------------|---------------------------------|");

            String amountToReturn = data[1][0];
            String loanBalance = data[2][0];
            String amountPaid = data[3][0];
            String loanProgressCalculation = data[4][0];
            String loanPerformanceCalculation = data[5][0];
            // Parse the values to integers
            double progressCalculation = Double.parseDouble(loanProgressCalculation);
            double performanceCalculation = Double.parseDouble(loanPerformanceCalculation);

            System.out.printf("%-17s %-13s %-9s %9.2f%% %10.2f%%\n", amountToReturn, amountPaid, loanBalance,
                    progressCalculation, performanceCalculation);
            System.out.println(
                    "|----------------------------------------------------------------------------------------|");
        }

        // Print Payment Details
        System.out.println("                           Payment Details");

        String[][] data1 = bool.get(1);

        // Check if paymentDataArray contains the "No payment data found" message
        if (data1.length == 1 && data1[0][0].equals("No payment data found")) {
            System.out.println(
                    "|----------------------------------------------------------------------------------------|");
            System.out.println("No payment data found");
            System.out.println(
                    "|----------------------------------------------------------------------------------------|");
        } else {
            System.out.println(
                    "|----------------------------------------------------------------------------------------|");

            System.out.println("Date            Amount");
            System.out.println(
                    "|----------------------------------------------------------------------------------------|");
            for (String[] rowData : data1) {
                String paymentDate = rowData[0];
                String paymentAmount = rowData[1];

                // Assuming the data is present in the bool1 array
                System.out.printf("%-15s %s\n", paymentDate, paymentAmount);
            }
            System.out.println(
                    "|----------------------------------------------------------------------------------------|");
        }

        // Print Deposit Details
        System.out.println("                              Deposit Details");

        String[][] deposits = bool2.get(0);
        // Check if depositArray contains the "No deposit data found" message
        if (deposits.length == 1 && deposits[0][0].equals("No deposit data found")) {
            System.out.println(
                    "|----------------------------------------------------------------------------------------|");
            System.out.println("No deposit data found");
            System.out.println(
                    "|----------------------------------------------------------------------------------------|");
        } else {
            System.out.println(
                    "|----------------------------------------------------------------------------------------|");

            System.out.println("Date            Amount");
            System.out.println(
                    "|----------------------------------------------------------------------------------------|");

            for (String[] rowData : deposits) {
                String datedeposited = rowData[0];
                String amount = rowData[1];

                // Assuming the data is present in the bool1 array
                System.out.printf("%-15s %s\n", datedeposited, amount);
            }
            System.out.println(
                    "|----------------------------------------------------------------------------------------|");
        }

        // Print Contribution Status

        System.out.println("                              Contribution Status");
        System.out.println(
                "|----------------------------------------------------------------------------------------|");
        System.out.println("Total Deposits    Contribution Performance");
        System.out.println(
                "|----------------------------------------------------------------------------------------|");

        String[][] user = bool2.get(1);

        String totalDeposits = user[0][0];
        String contributionPerformanceCalculation = user[1][0];
        // Parse the value to a double
        double contributionCalculation = Double.parseDouble(contributionPerformanceCalculation);

        System.out.printf("%-17s %.2f%%\n", totalDeposits, contributionCalculation);

        System.out.println(
                "|----------------------------------------------------------------------------------------|");

    }

    public static void requestLoan(String[] loan) throws Exception {
        String loandata[] = new String[4];
        loandata[0] = loan[0];
        loandata[1] = memberNumber;
        loandata[2] = loan[1];
        loandata[3] = loan[2];
        String bool[] = socketMethod(loandata);
        for (int index = 0; index < 1; index++) {
            if (bool[index] != null) {
                System.out.println(
                        "-------------" + "Your loan Application Number is:  " + bool[0] + "------------------");
                System.out
                        .println("--------------Please use it later to check your loan request status---------------");
                System.out.println("\n\n");
            }

        }

        showMainMenu();
    }

    public static void loanRequestStatus(String[] loanstatus) throws Exception {
        String loaninfo[] = socketMethod(loanstatus);
        if (loaninfo[0].equals("Your loan was processed. please wait for the admin to approve")) {
            System.out.println("**********Your loan was processed. please wait for the admin to approve**********");
        } else {
            System.out.println("   ---------------------------------------------------");
            System.out.println("     |  Amount requested        : " + loaninfo[0]);
            System.out.println("     |  Amount given            : " + loaninfo[1]);
            System.out.println("     |  Amount to return        : " + loaninfo[2]);
            System.out.println("     |  Amount to return monthly: " + loaninfo[3]);
            System.out.println("     |  Receive Date            : " + loaninfo[4]);
            System.out.println("     |  Payment period          : " + loaninfo[5]);
            System.out.println("     |  Status                  : " + loaninfo[6]);
            System.out.println("   ---------------------------------------------------\n");

            System.out
                    .println("Enter your command in this format to accept loan: acceptLoan 'loan application number'");
            System.out
                    .println("\n -------------------------------------------OR---------------------------------------");
            System.out
                    .println("Enter your command in this format to reject loan: rejectLoan 'loan application number'");
            Scanner comm = new Scanner(System.in);
            String command = comm.nextLine();
            String[] commandin = command.split(" ");
            String commanding[] = { commandin[0], commandin[1], memberNumber };
            String[] loanResponse = socketMethod(commanding);
            if (loanResponse[0].equals("accepted")) {
                System.out.println("Operation successful, your loan number is " + loanResponse[1]);

            } else {
                System.out.println("Operation successful, your loan request has been successfully cancelled.");
                System.out.println("~~~~~~~~~~~" + loanResponse[0] + "~~~~~~~~~~~");
            }

        }

        showMainMenu();
    }

    public static void deposit(String[] depositdata) throws Exception {
        String userdata[] = new String[4];
        userdata[0] = "deposit";
        userdata[1] = depositdata[0];
        userdata[2] = depositdata[1];
        userdata[3] = depositdata[2];
        String[] deposits = socketMethod(depositdata);
        if (deposits.length == 1) {
            // Error message received
            String errorMessage = deposits[0];
            System.out.println(errorMessage);
        } else {
            // Successful deposit response

            String depo = "----------------------------------------------------------" + "\nDeposit verified!"
                    + "\n---------------------------------------------------------" +
                    "\nMember Number: " + deposits[0] + "\nDeposited Amount: " + deposits[1]
                    + "\nDate Deposited: " + deposits[2] + "\nCurrent Balance: " + deposits[3]
                    + "\nThank you for using the system.";

            System.out.println(depo);
        }

    }

    public static void loanProgress() {

    }

    public static void payLoan() {

    }

    public static void main(String[] args) throws Exception {
        System.out.println("YOU ARE WELCOME TO UPRISE SACCO\n");
        login();
    }
}
