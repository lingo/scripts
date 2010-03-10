#!/opt/local/bin/perl
use warnings;
use strict;

use DBI;
use Data::Dumper;
use Carp;
use Getopt::Std;

our %opt;

getopts('H:u:p:d:s:S:h', \%opt)
	or usage();
$opt{h} && usage();
$opt{d} || usage();
$opt{s} || usage();
$opt{H} ||= 'localhost';
$opt{S} ||= '/Applications/XAMPP/xamppfiles/var/mysql/mysql.sock';

print Dumper(\%opt);
my $mydb = DBI->connect("DBI:mysql:database=$opt{d};host=$opt{H};mysql_socket=$opt{S}",
			$opt{u}, $opt{p},
			{RaiseError=>1, PrintError=>1}
		);

my $sqlitedb = DBI->connect("DBI:SQLite:dbname=$opt{s}",
			'', '',
			{AutoCommit => 0, RaiseError => 1, PrintError => 1});


my $tables = $mydb->selectcol_arrayref(q{ SHOW TABLES });
print Dumper(\$tables);

for my $table (@$tables) {
	my $cols = $mydb->selectall_hashref(qq{ DESC `$table` }, 'Field');
	my $vstr = '?,' x scalar keys %$cols;
	$vstr =~ s/,$//;
	my @cols = sort keys %$cols;
	my $cstr = join(',', map { qq|"$_"| } @cols);
	#print Dumper($vstr);

	my $stm = $mydb->prepare(qq{ SELECT * FROM `$table` ORDER BY ID });
	$sqlitedb->do(qq{ DELETE FROM "$table" });
	print qq{ INSERT INTO "$table" ($cstr) VALUES($vstr) \n};
	my $inStm = $sqlitedb->prepare(qq{ INSERT INTO "$table" ($cstr) VALUES($vstr) });
	$stm->execute();
	while (my $row = $stm->fetchrow_hashref()) {	
		print Dumper(\$row);
		for (my $i=1; $i <= @cols; $i++) {
			$inStm->bind_param($i, $row->{$cols[$i-1]});
		}
		$inStm->execute();
	}
	$sqlitedb->commit();
}

sub usage {
	print "$0  -H HOST -u USER -p PASS -d DATABASE -s SQLITEFILE\n";
	die;
}
